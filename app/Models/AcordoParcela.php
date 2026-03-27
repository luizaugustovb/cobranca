<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcordoParcela extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'acordo_id',
        'numero',
        'valor',
        'vencimento',
        'status',
        'payment_id'
    ];

    protected $casts = [
        'vencimento' => 'date',
        'valor' => 'decimal:2',
    ];

    public function acordo()
    {
        return $this->belongsTo(Acordo::class);
    }
}
