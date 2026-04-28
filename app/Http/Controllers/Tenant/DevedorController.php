<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Devedor;
use App\Models\Cliente;
use Illuminate\Http\Request;

class DevedorController extends Controller
{
    public function index()
    {
        $devedores = Devedor::with('cliente')->paginate(10);
        return view('tenant.devedores.index', compact('devedores'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('tenant.devedores.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'nome' => 'required|string|max:255',
            'cpf_cnpj' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:9',
        ]);

        Devedor::create($validated);

        return redirect()->route('tenant.devedores')->with('success', 'Devedor cadastrado com sucesso!');
    }

    public function show(Devedor $devedor)
    {
        $devedor->load(['cliente', 'titulos', 'acordos', 'contatos']);
        return view('tenant.devedores.show', compact('devedor'));
    }

    public function edit(Devedor $devedor)
    {
        $clientes = Cliente::all();
        return view('tenant.devedores.edit', compact('devedor', 'clientes'));
    }

    public function update(Request $request, Devedor $devedor)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'nome' => 'required|string|max:255',
            'cpf_cnpj' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:9',
        ]);

        $devedor->update($validated);

        return redirect()->route('tenant.devedores')->with('success', 'Devedor atualizado com sucesso!');
    }

    public function destroy(Devedor $devedor)
    {
        $devedor->delete();
        return redirect()->route('tenant.devedores')->with('success', 'Devedor excluído com sucesso!');
    }
}
