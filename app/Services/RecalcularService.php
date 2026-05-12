<?php

namespace App\Services;

use App\Models\Devedor;
use Carbon\Carbon;

class RecalcularService
{
    /**
     * Recalcula multa, juros (+ IPCA) e honorários de todos os títulos vencidos
     * em aberto de um devedor, usando as taxas configuradas no cliente vinculado.
     *
     * @return int Quantidade de títulos atualizados
     */
    public function recalcularDevedor(Devedor $devedor): int
    {
        $devedor->loadMissing(['cliente', 'titulos']);

        $cliente = $devedor->cliente;
        if (!$cliente) {
            return 0;
        }

        $multaPerc   = (float) ($cliente->multa_percentual     ?? 0);
        $jurosMensal = (float) ($cliente->juros_mensal          ?? 0);
        $honPerc     = (float) ($cliente->honorarios_percentual ?? 0);
        $ipcaMensal  = (float) ($cliente->ipca_mensal           ?? 0);

        $atualizados = 0;

        foreach ($devedor->titulos->where('status', 'aberto') as $titulo) {
            $venc = Carbon::parse($titulo->vencimento);

            if (!$venc->isPast()) {
                continue;
            }

            $meses    = (int) $venc->diffInMonths(now());
            $original = (float) $titulo->valor_original;

            $titulo->update([
                'multa'      => round($original * $multaPerc   / 100, 2),
                'juros'      => round($original * $jurosMensal / 100 * $meses, 2)
                              + round($original * $ipcaMensal  / 100 * $meses, 2),
                'honorarios' => round($original * $honPerc     / 100, 2),
            ]);

            $atualizados++;
        }

        return $atualizados;
    }

    /**
     * Recalcula um conjunto de devedores pelos seus IDs.
     *
     * @param  int[]  $devedorIds
     * @return int Total de títulos atualizados
     */
    public function recalcularPorIds(array $devedorIds): int
    {
        if (empty($devedorIds)) {
            return 0;
        }

        $total = 0;
        $devedores = Devedor::with(['cliente', 'titulos'])
            ->whereIn('id', array_unique($devedorIds))
            ->get();

        foreach ($devedores as $devedor) {
            $total += $this->recalcularDevedor($devedor);
        }

        return $total;
    }
}
