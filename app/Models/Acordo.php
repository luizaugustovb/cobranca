<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acordo extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'devedor_id',
        'valor_original',
        'desconto',
        'valor_acordo',
        'entrada',
        'parcelas',
        'status',
        'forma_pagamento',
        'asaas_link',
    ];

    protected $casts = [
        'valor_original' => 'decimal:2',
        'desconto' => 'decimal:2',
        'valor_acordo' => 'decimal:2',
        'entrada' => 'decimal:2',
    ];

    /**
     * Relacionamento com devedor
     */
    public function devedor()
    {
        return $this->belongsTo(Devedor::class);
    }

    /**
     * Relacionamento com as parcelas do acordo
     */
    public function acordoParcelas()
    {
        return $this->hasMany(AcordoParcela::class);
    }

    /**
     * Relacionamento com pagamentos
     */
    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }

    /**
     * Títulos originais que foram negociados neste acordo
     */
    public function titulos()
    {
        return $this->hasMany(Titulo::class);
    }
}
