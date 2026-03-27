<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ImpersonationController extends Controller
{
    /**
     * Iniciar impersonação de um tenant
     */
    public function start(Tenant $tenant)
    {
        // Verifica se é Admin Geral (via middleware ou logicamente)
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        session(['impersonating_tenant_id' => $tenant->id]);
        
        return redirect()->route('dashboard')->with('success', "Você agora está visualizando como: {$tenant->name}");
    }

    /**
     * Parar impersonação e voltar ao Painel Admin
     */
    public function stop()
    {
        session()->forget('impersonating_tenant_id');
        
        return redirect()->route('dashboard')->with('success', 'Impersonação encerrada. Você voltou ao painel global.');
    }
}
