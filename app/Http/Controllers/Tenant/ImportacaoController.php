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
            'arquivo' => 'required|file|mimes:xlsx,csv,txt|max:10240',
            'tipo' => 'required|string|in:devedores,titulos,contratos',
        ]);

        $path = $request->file('arquivo')->store('imports');

        $importacao = $this->importService->create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'arquivo' => $path,
            'tipo' => $request->tipo,
        ]);

        // Em um sistema real, isso iria para uma fila (Queue)
        // Por agora, processamos de forma síncrona para demonstração
        $this->importService->process($importacao);

        return redirect()->route('tenant.importacoes')->with('success', 'Arquivo enviado e processado com sucesso!');
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
