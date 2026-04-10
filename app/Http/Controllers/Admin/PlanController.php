<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('valor')->get();
        return view('admin.planos.index', compact('plans'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'plans'                => 'required|array',
            'plans.*.id'           => 'required|integer|exists:plans,id',
            'plans.*.nome'         => 'required|string|max:100',
            'plans.*.valor'        => 'required|numeric|min:0',
            'plans.*.viicio_plan_id' => 'nullable|integer',
        ]);

        foreach ($request->input('plans') as $data) {
            Plan::where('id', $data['id'])->update([
                'nome'           => $data['nome'],
                'valor'          => $data['valor'],
                'viicio_plan_id' => $data['viicio_plan_id'] ?? null,
            ]);
        }

        return redirect()->route('admin.planos')->with('success', 'Valores dos planos atualizados com sucesso!');
    }
}
