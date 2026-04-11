<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Acordo;
use App\Models\Titulo;
use App\Models\Pagamento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    public function index()
    {
        return view('tenant.relatorios.index');
    }

    public function fluxoCaixa()
    {
        $tenantId = Auth::user()->tenant_id;

        // Recebimentos por mês (últimos 12 meses)
        $meses = collect();
        for ($i = 11; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $total = Pagamento::where('tenant_id', $tenantId)
                ->whereYear('data_pagamento', $mes->year)
                ->whereMonth('data_pagamento', $mes->month)
                ->sum('valor');
            $meses->push([
                'mes'   => $mes->translatedFormat('M/Y'),
                'valor' => (float) $total,
            ]);
        }

        // Títulos em aberto agrupados por mês de vencimento (próximos 3 meses)
        $previsao = collect();
        for ($i = 0; $i <= 2; $i++) {
            $mes = Carbon::now()->addMonths($i);
            $total = Titulo::where('tenant_id', $tenantId)
                ->where('status', 'aberto')
                ->whereYear('vencimento', $mes->year)
                ->whereMonth('vencimento', $mes->month)
                ->get()->sum(fn($t) => $t->valor_total);
            $previsao->push([
                'mes'   => $mes->translatedFormat('M/Y'),
                'valor' => (float) $total,
            ]);
        }

        $stats = [
            'total_recebido'     => Pagamento::where('tenant_id', $tenantId)->sum('valor'),
            'recebido_mes'       => Pagamento::where('tenant_id', $tenantId)
                ->whereYear('data_pagamento', now()->year)
                ->whereMonth('data_pagamento', now()->month)
                ->sum('valor'),
            'titulos_abertos'    => Titulo::where('tenant_id', $tenantId)->where('status', 'aberto')->count(),
            'valor_aberto'       => Titulo::where('tenant_id', $tenantId)->where('status', 'aberto')
                ->get()->sum(fn($t) => $t->valor_total),
            'titulos_vencidos'   => Titulo::where('tenant_id', $tenantId)
                ->where('status', 'aberto')
                ->where('vencimento', '<', now()->toDateString())
                ->count(),
            'valor_vencido'      => Titulo::where('tenant_id', $tenantId)
                ->where('status', 'aberto')
                ->where('vencimento', '<', now()->toDateString())
                ->get()->sum(fn($t) => $t->valor_total),
        ];

        // Pagamentos recentes
        $pagamentosRecentes = Pagamento::where('tenant_id', $tenantId)
            ->with(['acordo.devedor'])
            ->orderByDesc('data_pagamento')
            ->limit(20)
            ->get();

        return view('tenant.relatorios.fluxo-caixa', compact('meses', 'previsao', 'stats', 'pagamentosRecentes'));
    }

    public function eficiencia()
    {
        $tenantId = Auth::user()->tenant_id;

        $totalTitulos    = Titulo::where('tenant_id', $tenantId)->count();
        $titulosAbertos  = Titulo::where('tenant_id', $tenantId)->where('status', 'aberto')->count();
        $titulosPagos    = Titulo::where('tenant_id', $tenantId)->where('status', 'pago')->count();
        $titulosAcordo   = Titulo::where('tenant_id', $tenantId)->whereNotNull('acordo_id')->count();
        $titulosCancelados = Titulo::where('tenant_id', $tenantId)->where('status', 'cancelado')->count();

        $totalAcordos    = Acordo::where('tenant_id', $tenantId)->count();
        $acordosAtivos   = Acordo::where('tenant_id', $tenantId)->where('status', 'ativo')->count();
        $acordosConcluidos = Acordo::where('tenant_id', $tenantId)->where('status', 'concluido')->count();
        $acordosInad     = Acordo::where('tenant_id', $tenantId)->where('status', 'inadimplente')->count();

        $taxaConversao   = $totalTitulos > 0 ? round(($titulosAcordo / $totalTitulos) * 100, 1) : 0;
        $taxaRecuperacao = $totalTitulos > 0 ? round((($titulosPagos + $titulosAcordo) / $totalTitulos) * 100, 1) : 0;
        $taxaInadimplencia = $totalTitulos > 0 ? round(($titulosAbertos / $totalTitulos) * 100, 1) : 0;

        // Valor total negociado via acordos
        $valorNegociado  = Acordo::where('tenant_id', $tenantId)->sum('valor_acordo');
        $valorOriginal   = Acordo::where('tenant_id', $tenantId)->sum('valor_original');
        $descontoMedio   = $valorOriginal > 0 ? round(((($valorOriginal - $valorNegociado) / $valorOriginal) * 100), 1) : 0;

        // Acordos por mês (últimos 6 meses)
        $acordosPorMes = collect();
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $qtd = Acordo::where('tenant_id', $tenantId)
                ->whereYear('created_at', $mes->year)
                ->whereMonth('created_at', $mes->month)
                ->count();
            $acordosPorMes->push(['mes' => $mes->translatedFormat('M/Y'), 'qtd' => $qtd]);
        }

        // Últimos acordos
        $ultimosAcordos = Acordo::where('tenant_id', $tenantId)
            ->with('devedor')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        return view('tenant.relatorios.eficiencia', compact(
            'totalTitulos', 'titulosAbertos', 'titulosPagos', 'titulosAcordo', 'titulosCancelados',
            'totalAcordos', 'acordosAtivos', 'acordosConcluidos', 'acordosInad',
            'taxaConversao', 'taxaRecuperacao', 'taxaInadimplencia',
            'valorNegociado', 'valorOriginal', 'descontoMedio',
            'acordosPorMes', 'ultimosAcordos'
        ));
    }

    public function auditoria(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $query = AuditLog::where('tenant_id', $tenantId)
            ->with('user')
            ->orderByDesc('created_at');

        if ($request->filled('acao')) {
            $query->where('action', $request->acao);
        }
        if ($request->filled('usuario')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->usuario . '%'));
        }
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $logs = $query->paginate(30)->withQueryString();

        $acoes = AuditLog::where('tenant_id', $tenantId)
            ->distinct()
            ->pluck('action')
            ->sort()
            ->values();

        return view('tenant.relatorios.auditoria', compact('logs', 'acoes'));
    }
}

