<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Titulo extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'devedor_id',
        'acordo_id',
        'numero',
        'descricao',
        'valor_original',
        'juros',
        'multa',
        'desconto',
        'honorarios',
        'vencimento',
        'status'
    ];

    protected $casts = [
        'vencimento' => 'date',
        'valor_original' => 'decimal:2',
        'juros' => 'decimal:2',
        'multa' => 'decimal:2',
        'desconto' => 'decimal:2',
        'honorarios' => 'decimal:2',
    ];

    /**
     * Valor total = original + juros + multa + honorarios - desconto
     * (valores já armazenados no registro)
     */
    public function getValorTotalAttribute(): float
    {
        return (float) $this->valor_original
            + (float) $this->juros
            + (float) $this->multa
            + (float) $this->honorarios
            - (float) $this->desconto;
    }

    /**
     * Valor corrigido calculado DINAMICAMENTE pelas taxas do cliente,
     * com juros mensais acumulados desde o vencimento até hoje.
     *
     * Retorna array com detalhamento:
     *   valor_original, multa, juros_acumulado, honorarios, desconto, total, meses_atraso
     */
    public function getDetalhamentoCorrigidoAttribute(): array
    {
        $cliente = $this->devedor?->cliente;

        $multaPerc     = $cliente ? (float) $cliente->multa_percentual      : 0;
        $jurosMensal   = $cliente ? (float) $cliente->juros_mensal           : 0;
        $honPerc       = $cliente ? (float) $cliente->honorarios_percentual  : 0;

        $original  = (float) $this->valor_original;
        $desconto  = (float) $this->desconto;

        // Meses de atraso (0 se ainda não venceu)
        $meses = 0;
        if ($this->vencimento) {
            $venc = \Carbon\Carbon::parse($this->vencimento);
            if ($venc->isPast()) {
                $meses = (int) $venc->diffInMonths(now());
            }
        }

        $multa          = round($original * $multaPerc   / 100, 2);
        $jurosAcumulado = round($original * $jurosMensal / 100 * $meses, 2);
        $honorarios     = round($original * $honPerc     / 100, 2);

        $total = $original + $multa + $jurosAcumulado + $honorarios - $desconto;

        return [
            'valor_original'   => $original,
            'multa'            => $multa,
            'multa_percentual' => $multaPerc,
            'juros_acumulado'  => $jurosAcumulado,
            'juros_mensal'     => $jurosMensal,
            'meses_atraso'     => $meses,
            'honorarios'       => $honorarios,
            'hon_percentual'   => $honPerc,
            'desconto'         => $desconto,
            'total'            => max(0, $total),
        ];
    }

    /** Atalho: total corrigido */
    public function getValorCorrigidoAttribute(): float
    {
        return $this->detalhamentoCorrigido['total'];
    }

    public function devedor()
    {
        return $this->belongsTo(Devedor::class);
    }

    public function acordo()
    {
        return $this->belongsTo(\App\Models\Acordo::class);
    }

    public function historicoStatus()
    {
        return $this->hasMany(StatusCobranca::class);
    }
}
