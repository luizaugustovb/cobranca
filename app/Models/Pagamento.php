<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'acordo_id',
        'parcela_id',
        'valor',
        'data_pagamento',
        'forma_pagamento',
        'gateway_id'
    ];

    protected $casts = [
        'data_pagamento' => 'datetime',
        'valor' => 'decimal:2',
    ];

    public function acordo()
    {
        return $this->belongsTo(Acordo::class);
    }

    public function parcela()
    {
        return $this->belongsTo(AcordoParcela::class);
    }
}
