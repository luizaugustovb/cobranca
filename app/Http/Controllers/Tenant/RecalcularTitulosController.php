<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Devedor;
use App\Models\Titulo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecalcularTitulosController extends Controller
{
    /**
     * Recalcula e persiste juros, multa, IPCA e honorários nos títulos em aberto de um devedor.
     * Apenas títulos com status 'aberto' e vencidos são recalculados.
     */
    public function recalcular(Request $request, Devedor $devedor)
    {
        $devedor->load(['cliente', 'titulos']);

        $cliente = $devedor->cliente;

        if (!$cliente) {
            return back()->with('error', 'Devedor não possui cliente vinculado.');
        }

        $multaPerc   = (float) ($cliente->multa_percentual      ?? 0);
        $jurosMensal = (float) ($cliente->juros_mensal           ?? 0);
        $honPerc     = (float) ($cliente->honorarios_percentual  ?? 0);
        $ipcaMensal  = (float) ($cliente->ipca_mensal            ?? 0);

        $atualizados = 0;

        foreach ($devedor->titulos->where('status', 'aberto') as $titulo) {
            $venc = Carbon::parse($titulo->vencimento);

            // Só recalcula títulos vencidos
            if (!$venc->isPast()) {
                continue;
            }

            $meses    = (int) $venc->diffInMonths(now());
            $original = (float) $titulo->valor_original;

            $multa          = round($original * $multaPerc   / 100, 2);
            $jurosAcumulado = round($original * $jurosMensal / 100 * $meses, 2);
            $correcaoIpca   = round($original * $ipcaMensal  / 100 * $meses, 2);
            $honorarios     = round($original * $honPerc     / 100, 2);

            $titulo->update([
                'multa'      => $multa,
                'juros'      => $jurosAcumulado + $correcaoIpca,  // juros + IPCA ficam no campo juros
                'honorarios' => $honorarios,
            ]);

            $atualizados++;
        }

        $msg = $atualizados > 0
            ? "{$atualizados} título(s) recalculado(s) com sucesso."
            : 'Nenhum título vencido encontrado para recalcular.';

        return back()->with('success', $msg);
    }
}
