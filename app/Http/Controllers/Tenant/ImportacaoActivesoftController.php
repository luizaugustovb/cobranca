<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Devedor;
use App\Models\Aluno;
use App\Models\Titulo;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ImportacaoActivesoftController extends Controller
{
    private string $pythonBin;
    private string $scriptPath;

    public function __construct()
    {
        $venvBin = PHP_OS_FAMILY === 'Windows'
            ? base_path('.venv/Scripts/python.exe')
            : base_path('.venv/bin/python3');

        $this->pythonBin  = file_exists($venvBin) ? $venvBin : (PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3');
        $this->scriptPath = base_path('scripts/activesoft_parser.py');
    }

    /**
     * Exibe formulário de upload do PDF Activesoft.
     */
    public function create()
    {
        return view('tenant.importacoes.activesoft-upload');
    }

    /**
     * Processa o PDF e exibe o preview editável.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:pdf|max:20480',
        ], [
            'arquivo.required' => 'Selecione um arquivo PDF.',
            'arquivo.mimes'    => 'Somente arquivos PDF são aceitos.',
            'arquivo.max'      => 'O arquivo não pode ultrapassar 20 MB.',
        ]);

        $file    = $request->file('arquivo');
        $pdfPath = $file->getRealPath();

        $cmd = sprintf(
            '%s %s --pdf %s',
            escapeshellarg($this->pythonBin),
            escapeshellarg($this->scriptPath),
            escapeshellarg($pdfPath)
        );

        $output   = [];
        $exitCode = 0;
        exec($cmd . ' 2>&1', $output, $exitCode);

        $json   = implode('', $output);
        $result = json_decode($json, true);

        if (!$result || !($result['success'] ?? false)) {
            $error = $result['error'] ?? $json;
            return back()->withErrors(['arquivo' => 'Falha ao processar PDF: ' . $error]);
        }

        $responsaveis = $result['responsaveis'] ?? [];

        if (empty($responsaveis)) {
            return back()->withErrors(['arquivo' => 'Nenhum responsável encontrado no PDF. Verifique se o arquivo é do formato Activesoft.']);
        }

        // Salva em arquivo temporário (JSON)
        $key = 'activesoft_' . auth()->id() . '_' . time();
        Storage::put("temp/{$key}.json", json_encode($responsaveis));

        return view('tenant.importacoes.activesoft-preview', compact('responsaveis', 'key'));
    }

    /**
     * Confirma e importa os dados do preview para o banco.
     */
    public function confirmar(Request $request)
    {
        $request->validate(['key' => 'required|string|regex:/^[a-z0-9_]+$/']);

        $key  = $request->input('key');
        $path = "temp/{$key}.json";

        if (!Storage::exists($path)) {
            return back()->withErrors(['key' => 'Sessão expirada. Envie o PDF novamente.']);
        }

        Storage::delete($path);

        // Dados enviados pelo formulário de preview (editáveis)
        $itens = $request->input('itens', []);

        if (empty($itens)) {
            return back()->withErrors(['itens' => 'Nenhum item selecionado para importação.']);
        }

        $tenantId = auth()->user()->tenant_id;

        // Carrega regra de honorários
        $honTipo  = Setting::where('tenant_id', $tenantId)->where('key', 'honorarios_tipo')->value('value') ?? 'fixo';
        $honValor = (float)(Setting::where('tenant_id', $tenantId)->where('key', 'honorarios_valor')->value('value') ?? 0);

        $importados = 0;
        $erros      = 0;
        $erroDetalhe = [];

        foreach ($itens as $idx => $item) {
            // Chave de seleção: se o checkbox não veio, skip
            if (empty($item['importar'])) {
                continue;
            }

            try {
                $cpf     = preg_replace('/[^0-9]/', '', $item['cpf'] ?? '');
                $nome    = trim($item['nome'] ?? '');
                $telefone = trim($item['telefone'] ?? '');

                if (empty($cpf) || empty($nome)) {
                    throw new \Exception("CPF ou nome do responsável vazio.");
                }

                // Upsert Cliente
                $cliente = Cliente::updateOrCreate(
                    ['tenant_id' => $tenantId, 'documento' => $cpf],
                    [
                        'nome'     => $nome,
                        'telefone' => $telefone ?: null,
                        'email'    => null,
                        'endereco' => implode(', ', array_filter([
                            trim($item['rua'] ?? ''),
                            trim($item['numero'] ?? ''),
                            trim($item['bairro'] ?? ''),
                            trim($item['cidade'] ?? ''),
                            trim($item['estado'] ?? ''),
                        ])),
                    ]
                );

                // Upsert Devedor
                $devedor = Devedor::updateOrCreate(
                    ['tenant_id' => $tenantId, 'cpf_cnpj' => $cpf],
                    [
                        'cliente_id' => $cliente->id,
                        'nome'       => $nome,
                        'telefone'   => $telefone ?: null,
                        'rua'        => trim($item['rua'] ?? '') ?: null,
                        'numero'     => trim($item['numero'] ?? '') ?: null,
                        'bairro'     => trim($item['bairro'] ?? '') ?: null,
                        'cidade'     => trim($item['cidade'] ?? '') ?: null,
                        'estado'     => trim($item['estado'] ?? '') ?: null,
                        'cep'        => preg_replace('/[^0-9]/', '', $item['cep'] ?? '') ?: null,
                    ]
                );

                // Aluno
                $nomeAluno = trim($item['aluno'] ?? '');
                if ($nomeAluno) {
                    Aluno::updateOrCreate(
                        ['tenant_id' => $tenantId, 'devedor_id' => $devedor->id, 'nome' => $nomeAluno],
                        ['matricula' => null, 'curso' => trim($item['servico'] ?? '') ?: null]
                    );
                }

                // Título
                $numeroTitulo = trim($item['numero_titulo'] ?? '');
                $parcela      = trim($item['parcela'] ?? '');
                $numeroFinal  = $parcela ? "{$numeroTitulo}/{$parcela}" : $numeroTitulo;

                $valor = $this->parseValor($item['valor_servico'] ?? '0');

                if (empty($numeroFinal) || $valor <= 0) {
                    throw new \Exception("Número do título ou valor inválido.");
                }

                $vencimento = $this->parseData($item['vencimento'] ?? '');
                if (!$vencimento) {
                    throw new \Exception("Data de vencimento inválida: " . ($item['vencimento'] ?? ''));
                }

                $multaJuros = $this->parseValor($item['multa_juros'] ?? '0');

                // Honorários
                $honorarios = 0;
                if ($honValor > 0) {
                    $honorarios = $honTipo === 'percentual'
                        ? round($valor * $honValor / 100, 2)
                        : $honValor;
                }

                Titulo::updateOrCreate(
                    ['tenant_id' => $tenantId, 'numero' => $numeroFinal],
                    [
                        'devedor_id'     => $devedor->id,
                        'descricao'      => trim($item['servico'] ?? '') ?: ($nomeAluno ? "Serviço — {$nomeAluno}" : null),
                        'valor_original' => $valor,
                        'juros'          => $multaJuros,
                        'multa'          => 0,
                        'desconto'       => 0,
                        'honorarios'     => $honorarios,
                        'vencimento'     => $vencimento,
                        'status'         => strtolower($item['situacao'] ?? '') === 'em aberto' ? 'aberto' : 'pago',
                    ]
                );

                $importados++;
            } catch (\Exception $e) {
                $erros++;
                $erroDetalhe[] = "Item #{$idx}: " . $e->getMessage();
                Log::error("Activesoft import item #{$idx}: " . $e->getMessage());
            }
        }

        $msg = "Importação Activesoft concluída: {$importados} título(s) importado(s)";
        if ($erros) {
            $msg .= " | {$erros} item(ns) com erro: " . implode('; ', array_slice($erroDetalhe, 0, 3));
        }

        return redirect()->route('tenant.importacoes')->with('success', $msg);
    }

    // -----------------------------------------------------------------------

    private function parseValor($value): float
    {
        if (is_null($value) || $value === '' || $value === '--') return 0.0;
        $clean = preg_replace('/[^\d,.]/', '', (string) $value);
        // Troca vírgula por ponto (formato brasileiro)
        $clean = str_replace(',', '.', preg_replace('/\.(?=.*\.)/', '', $clean));
        return (float) $clean;
    }

    private function parseData($value): ?string
    {
        if (empty($value)) return null;
        try {
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            // fall through
        }
        return null;
    }
}
