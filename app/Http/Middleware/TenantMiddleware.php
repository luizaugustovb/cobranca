<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            // Se o usuário já tem um tenant_id (não é admin geral ou está vinculado)
            if ($user->tenant_id) {
                session(['tenant_id' => $user->tenant_id]);
            } 
            // Se for admin geral e estiver tentando acessar um tenant (impersonação ou seleção)
            elseif (session('impersonating_tenant_id')) {
                session(['tenant_id' => session('impersonating_tenant_id')]);
            }
            // Se for admin geral no painel admin, não seta tenant_id (vê tudo)
            else {
                session()->forget('tenant_id');
            }
        }

        return $next($request);
    }
}
