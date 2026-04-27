<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Pagamento;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::paginate(10);
        return view('tenant.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('tenant.clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'documento' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
        ]);

        Cliente::create($validated);

        return redirect()->route('tenant.clientes')->with('success', 'Cliente criado com sucesso!');
    }

    public function edit(Cliente $cliente)
    {
        return view('tenant.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'documento' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
        ]);

        $cliente->update($validated);

        return redirect()->route('tenant.clientes')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('tenant.clientes')->with('success', 'Cliente excluído com sucesso!');
    }

    public function relatorio(Request $request, Cliente $cliente)
    {
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->toDateString());
        $dataFim    = $request->get('data_fim',    now()->toDateString());

        // Pagamentos do período vinculados a devedores deste cliente
        $pagamentos = Pagamento::with(['acordo.devedor', 'parcela'])
            ->whereHas('acordo.devedor', fn($q) => $q->where('cliente_id', $cliente->id))
            ->whereBetween('data_pagamento', [$dataInicio, $dataFim . ' 23:59:59'])
            ->orderBy('data_pagamento')
            ->get();

        // Agrupado por devedor
        $porDevedor = $pagamentos->groupBy(fn($p) => $p->acordo->devedor_id ?? 0)
            ->map(function ($pags) {
                $devedor = $pags->first()->acordo->devedor ?? null;
                return [
                    'devedor'  => $devedor,
                    'pagamentos' => $pags,
                    'total'    => $pags->sum('valor'),
                ];
            })->values();

        $totalGeral  = $pagamentos->sum('valor');
        $settings    = Setting::all()->pluck('value', 'key');

        return view('tenant.clientes.relatorio', compact(
            'cliente',
            'pagamentos',
            'porDevedor',
            'totalGeral',
            'dataInicio',
            'dataFim',
            'settings'
        ));
    }

    public function relatorioPdf(Request $request, Cliente $cliente)
    {
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->toDateString());
        $dataFim    = $request->get('data_fim',    now()->toDateString());

        $pagamentos = Pagamento::with(['acordo.devedor', 'parcela'])
            ->whereHas('acordo.devedor', fn($q) => $q->where('cliente_id', $cliente->id))
            ->whereBetween('data_pagamento', [$dataInicio, $dataFim . ' 23:59:59'])
            ->orderBy('data_pagamento')
            ->get();

        $porDevedor = $pagamentos->groupBy(fn($p) => $p->acordo->devedor_id ?? 0)
            ->map(function ($pags) {
                $devedor = $pags->first()->acordo->devedor ?? null;
                return [
                    'devedor'  => $devedor,
                    'pagamentos' => $pags,
                    'total'    => $pags->sum('valor'),
                ];
            })->values();

        $totalGeral = $pagamentos->sum('valor');
        $settings   = Setting::all()->pluck('value', 'key');

        $html = view('tenant.clientes.relatorio-pdf', compact(
            'cliente',
            'porDevedor',
            'totalGeral',
            'dataInicio',
            'dataFim',
            'settings'
        ))->render();

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('X-Content-For-Print', '1');
    }
}
