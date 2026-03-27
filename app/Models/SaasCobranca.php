<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaasCobranca extends Model
{
    protected $fillable = [
        'tenant_id',
        'valor',
        'vencimento',
        'data_pagamento',
        'status',
        'asaas_id',
        'link_pagamento',
    ];

    protected $casts = [
        'vencimento' => 'date',
        'data_pagamento' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
