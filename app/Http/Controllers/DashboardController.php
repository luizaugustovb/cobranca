<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Titulo;
use App\Models\Acordo;
use App\Models\Pagamento;
use App\Models\Devedor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Se estiver no contexto de um tenant (logado ou impersonando)
        if ($user->tenant_id || session('impersonating_tenant_id')) {
            return $this->tenantDashboard();
        }

        // Se for Admin Geral no contexto global
        if ($user->is_admin) {
            return $this->adminDashboard();
        }

        abort(403);
    }

    private function tenantDashboard()
    {
        $tenant = auth()->user()->tenant;
        $tenantName = $tenant?->name;

        $totais = [
            'titulos_abertos' => Titulo::where('status', 'aberto')->count(),
            'valor_aberto' => Titulo::where('status', 'aberto')->sum('valor_original'),
            'acordos_ativos' => Acordo::where('status', 'ativo')->count(),
            'pagamentos_mes' => Pagamento::whereMonth('data_pagamento', now()->month)->sum('valor'),
            'devedores' => Devedor::count(),
            'titulos_vencidos_hoje' => Titulo::where('status', 'aberto')->whereDate('vencimento', today())->count(),
        ];

        return view('dashboard', compact('totais', 'tenantName'));
    }

    private function adminDashboard()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'total_users' => User::count(),
            'global_revenue' => Pagamento::sum('valor'),
        ];

        $recentTenants = Tenant::latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentTenants'));
    }
}
