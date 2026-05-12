<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use App\Models\Devedor;
use App\Models\Aluno;
use App\Models\Titulo;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Importação unificada — um único arquivo com responsável + títulos + alunos
 *
 * Colunas esperadas na planilha (cabeçalho na linha 1):
 *   responsavel | cpf | contato | email | rua | numero_end | cep |
 *   aluno | matricula | servico | numero_titulo | vencimento |
 *   valor | multa | juros
 */
class CobrancaImport implements ToCollection, WithHeadingRow, WithProgressBar, SkipsEmptyRows
{
    private int $tenantId;
    private int $userId;
    private int $clienteId;
    public int $importados = 0;
    public int $erros = 0;
    public array $erroDetalhe = [];

    private string $honorariosTipo;
    private float $honorariosValor;

    public function __construct(int $tenantId, int $userId, int $clienteId)
    {
        $this->tenantId  = $tenantId;
        $this->userId    = $userId;
        $this->clienteId = $clienteId;

        // Carrega regra de honorários configurada pelo tenant
        $this->honorariosTipo  = Setting::where('tenant_id', $tenantId)->where('key', 'honorarios_tipo')->value('value') ?? 'fixo';
        $this->honorariosValor = (float) (Setting::where('tenant_id', $tenantId)->where('key', 'honorarios_valor')->value('value') ?? 0);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                $row = $row->toArray();

                // --- Responsável Financeiro ---
                $cpf  = $this->sanitize($row['cpf'] ?? $row['cpf_cnpj'] ?? null);
                $nome = trim($row['responsavel'] ?? $row['nome_responsavel'] ?? $row['nome'] ?? '');

                if (empty($cpf) || empty($nome)) {
                    $this->erros++;
                    $this->erroDetalhe[] = "Linha " . ($index + 2) . ": CPF ou nome do responsável vazio.";
                    continue;
                }

                $fone   = $this->sanitize($row['contato'] ?? $row['telefone'] ?? null);
                $email  = trim($row['email'] ?? '');
                $rua    = trim($row['rua'] ?? $row['endereco'] ?? '');
                $numEnd = trim($row['numero_end'] ?? $row['num'] ?? '');
                $cep    = $this->sanitize($row['cep'] ?? null);

                // Upsert Devedor — vinculado ao Cliente selecionado pelo usuário
                $devedor = Devedor::updateOrCreate(
                    ['tenant_id' => $this->tenantId, 'cpf_cnpj' => $cpf],
                    [
                        'cliente_id' => $this->clienteId,
                        'nome'       => $nome,
                        'telefone'   => $fone ?: null,
                        'email'      => $email ?: null,
                        'rua'        => $rua ?: null,
                        'numero'     => $numEnd ?: null,
                        'cep'        => $cep ?: null,
                    ]
                );

                // --- Aluno ---
                $nomeAluno = trim($row['aluno'] ?? $row['nome_aluno'] ?? '');
                $matricula = trim($row['matricula'] ?? '');
                $servico   = trim($row['servico'] ?? $row['servico_prestado'] ?? '');

                if ($nomeAluno) {
                    Aluno::updateOrCreate(
                        ['tenant_id' => $this->tenantId, 'devedor_id' => $devedor->id, 'nome' => $nomeAluno],
                        ['matricula' => $matricula ?: null, 'curso' => $servico ?: null]
                    );
                }

                // --- Título ---
                $numeroTitulo = trim($row['numero_titulo'] ?? $row['titulo'] ?? $row['numero'] ?? '');
                $valorRaw     = $row['valor'] ?? $row['valor_servico'] ?? null;
                $valor        = $this->parseValor($valorRaw);

                if (empty($numeroTitulo) || $valor <= 0) {
                    $this->erros++;
                    $this->erroDetalhe[] = "Linha " . ($index + 2) . ": Número do título ou valor inválido.";
                    continue;
                }

                $vencimento = $this->parseData($row['vencimento'] ?? null);
                if (!$vencimento) {
                    $this->erros++;
                    $this->erroDetalhe[] = "Linha " . ($index + 2) . ": Data de vencimento inválida.";
                    continue;
                }

                // Honorários: usa o valor da planilha se informado; senão aplica regra configurada
                $honorariosRaw = $this->parseValor($row['honorarios'] ?? $row['honorarios_advocaticios'] ?? 0);
                if ($honorariosRaw == 0 && $this->honorariosValor > 0) {
                    $honorariosRaw = $this->honorariosTipo === 'percentual'
                        ? round($valor * $this->honorariosValor / 100, 2)
                        : $this->honorariosValor;
                }

                Titulo::updateOrCreate(
                    ['tenant_id' => $this->tenantId, 'numero' => $numeroTitulo],
                    [
                        'devedor_id'     => $devedor->id,
                        'descricao'      => $servico ?: ($nomeAluno ? "Serviço — {$nomeAluno}" : null),
                        'valor_original' => $valor,
                        'juros'          => $this->parseValor($row['juros'] ?? 0),
                        'multa'          => $this->parseValor($row['multa'] ?? 0),
                        'desconto'       => $this->parseValor($row['desconto'] ?? 0),
                        'honorarios'     => $honorariosRaw,
                        'vencimento'     => $vencimento,
                        'status'         => 'aberto',
                    ]
                );

                $this->importados++;
            } catch (\Exception $e) {
                $this->erros++;
                $this->erroDetalhe[] = "Linha " . ($index + 2) . ": " . $e->getMessage();
                Log::error("Importação linha " . ($index + 2) . ": " . $e->getMessage());
            }
        }
    }

    private function sanitize(?string $value): string
    {
        return preg_replace('/[^0-9]/', '', (string) $value);
    }

    private function parseValor($value): float
    {
        if (is_null($value)) return 0;
        // Remove R$, pontos de milhar, troca vírgula por ponto
        $clean = preg_replace('/[^\d,.]/', '', (string) $value);
        $clean = str_replace(',', '.', preg_replace('/\.(?=.*\.)/', '', $clean));
        return (float) $clean;
    }

    private function parseData($value): ?string
    {
        if (empty($value)) return null;
        try {
            // Excel armazena datas como inteiro serial
            if (is_numeric($value)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
            }
            // Tenta formatos BR e ISO
            foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y'] as $fmt) {
                try {
                    return Carbon::createFromFormat($fmt, trim($value))->format('Y-m-d');
                } catch (\Exception $e) {
                }
            }
        } catch (\Exception $e) {
        }
        return null;
    }
}
