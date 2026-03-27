<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Titulo;
use App\Models\Devedor;
use Illuminate\Http\Request;

class TituloController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'aberto');
        
        $titulos = Titulo::with('devedor')
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->paginate(15);
            
        return view('tenant.titulos.index', compact('titulos', 'status'));
    }

    public function create()
    {
        $devedores = Devedor::all();
        return view('tenant.titulos.create', compact('devedores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'devedor_id' => 'required|exists:devedores,id',
            'numero' => 'required|string|max:50',
            'valor_original' => 'required|numeric|min:0.01',
            'vencimento' => 'required|date',
            'status' => 'required|string|in:aberto,pago,cancelado',
            'juros' => 'nullable|numeric|min:0',
            'multa' => 'nullable|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'honorarios' => 'nullable|numeric|min:0',
        ]);

        Titulo::create($validated);

        return redirect()->route('tenant.titulos')->with('success', 'Título gerado com sucesso!');
    }

    public function edit(Titulo $titulo)
    {
        $devedores = Devedor::all();
        return view('tenant.titulos.edit', compact('titulo', 'devedores'));
    }

    public function update(Request $request, Titulo $titulo)
    {
        $validated = $request->validate([
            'devedor_id' => 'required|exists:devedores,id',
            'numero' => 'required|string|max:50',
            'valor_original' => 'required|numeric|min:0.01',
            'vencimento' => 'required|date',
            'status' => 'required|string|in:aberto,pago,cancelado',
            'juros' => 'nullable|numeric|min:0',
            'multa' => 'nullable|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'honorarios' => 'nullable|numeric|min:0',
        ]);

        $titulo->update($validated);

        return redirect()->route('tenant.titulos')->with('success', 'Título atualizado com sucesso!');
    }
}
