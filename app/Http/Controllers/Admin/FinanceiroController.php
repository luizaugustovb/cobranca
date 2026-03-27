<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaasCobranca;
use Illuminate\Http\Request;

class FinanceiroController extends Controller
{
    public function index()
    {
        // Pega as cobranças que o SaaS fez para os Tenants
        $cobrancas = SaasCobranca::with('tenant')->latest()->paginate(10);
        
        $totais = [
            'recebido' => SaasCobranca::where('status', 'pago')->sum('valor'),
            'pendente' => SaasCobranca::where('status', 'pendente')->sum('valor'),
            'vencido' => SaasCobranca::where('status', 'vencido')->sum('valor'),
        ];

        return view('admin.financeiro.index', compact('cobrancas', 'totais'));
    }
}
