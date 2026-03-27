<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Importacao;
use App\Services\ImportacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportacaoController extends Controller
{
    private $importService;

    public function __construct(ImportacaoService $importService)
    {
        $this->importService = $importService;
    }

    public function index()
    {
        $importacoes = Importacao::orderBy('created_at', 'desc')->paginate(10);
        return view('tenant.importacoes.index', compact('importacoes'));
    }

    public function create()
    {
        return view('tenant.importacoes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $path = $request->file('arquivo')->store('imports');

        $importacao = $this->importService->create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id'   => auth()->id(),
            'arquivo'   => $path,
            'tipo'      => 'cobrancas',
        ]);

        [$importados, $erros, $erroDetalhe] = $this->importService->process($importacao);

        $msg = "Importação concluída: {$importados} título(s) processado(s)";
        if ($erros) {
            $msg .= " | {$erros} linha(s) com erro.";
        }

        return redirect()->route('tenant.importacoes')->with('success', $msg);
    }

    public function template()
    {
        $headers = [
            'responsavel', 'cpf', 'contato', 'email',
            'rua', 'numero_end', 'cep',
            'aluno', 'matricula', 'servico',
            'numero_titulo', 'vencimento', 'valor', 'multa', 'juros', 'honorarios',
        ];

        $exemplo = [
            'João da Silva', '123.456.789-09', '(11) 91234-5678', 'joao@email.com',
            'Rua das Flores', '123', '01310-100',
            'Maria da Silva', 'MAT001', 'Mensalidade Escolar',
            'TIT-2024-001', '31/12/2024', '1500,00', '2,00', '1,00', '10,00',
        ];

        $csv = implode(';', $headers) . "\n" . implode(';', $exemplo) . "\n";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=modelo_importacao_cobrancas.csv',
        ]);
    }

    public function show(Importacao $importacao)
    {
        $importacao->load('itens');
        return view('tenant.importacoes.show', compact('importacao'));
    }

    public function download(Importacao $importacao)
    {
        return Storage::download($importacao->arquivo);
    }
}
