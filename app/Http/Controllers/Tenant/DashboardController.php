<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Titulo;
use App\Models\Acordo;
use App\Models\Pagamento;
use App\Models\Devedor;

class DashboardController extends Controller
{
    public function index()
    {
        // Totais filtrados pelo Tenant (via TenantScope)
        $totais = [
            'titulos_abertos' => Titulo::where('status', 'aberto')->count(),
            'valor_aberto' => Titulo::where('status', 'aberto')->sum('valor_original'),
            'acordos_ativos' => Acordo::where('status', 'ativo')->count(),
            'pagamentos_mes' => Pagamento::whereMonth('data_pagamento', now()->month)->sum('valor'),
            'devedores' => Devedor::count(),
        ];

        return view('dashboard', compact('totais'));
    }
}
