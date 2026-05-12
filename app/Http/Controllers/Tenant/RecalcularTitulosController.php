<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Devedor;
use App\Services\RecalcularService;
use Illuminate\Http\Request;

class RecalcularTitulosController extends Controller
{
    private RecalcularService $recalcular;

    public function __construct(RecalcularService $recalcular)
    {
        $this->recalcular = $recalcular;
    }

    /**
     * Recalcula e persiste juros, multa, IPCA e honorários nos títulos em aberto de um devedor.
     */
    public function recalcular(Request $request, Devedor $devedor)
    {
        if (!$devedor->cliente) {
            return back()->with('error', 'Devedor não possui cliente vinculado.');
        }

        $atualizados = $this->recalcular->recalcularDevedor($devedor);

        $msg = $atualizados > 0
            ? "{$atualizados} título(s) recalculado(s) com sucesso."
            : 'Nenhum título vencido encontrado para recalcular.';

        return back()->with('success', $msg);
    }
}
