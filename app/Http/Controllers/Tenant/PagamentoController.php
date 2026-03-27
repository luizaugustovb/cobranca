<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Pagamento;
use App\Models\Acordo;
use App\Models\AcordoParcela;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
    public function index()
    {
        $pagamentos = Pagamento::with(['acordo.devedor', 'parcela'])
            ->orderBy('data_pagamento', 'desc')
            ->paginate(15);
            
        return view('tenant.pagamentos.index', compact('pagamentos'));
    }

    public function create()
    {
        // Registro manual de pagamento (ex: dinheiro ou pix direto)
        $acordos = Acordo::where('status', 'ativo')->with('devedor')->get();
        return view('tenant.pagamentos.create', compact('acordos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'acordo_id' => 'required|exists:acordos,id',
            'parcela_id' => 'nullable|exists:acordo_parcelas,id',
            'valor' => 'required|numeric|min:0.01',
            'data_pagamento' => 'required|date',
            'forma_pagamento' => 'required|string',
            'gateway_id' => 'nullable|string',
        ]);

        Pagamento::create($validated);

        // Se houver parcela, marca como paga
        if ($request->parcela_id) {
            AcordoParcela::find($request->parcela_id)->update(['status' => 'pago']);
        }

        return redirect()->route('tenant.pagamentos')->with('success', 'Pagamento registrado com sucesso!');
    }
}
