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

    public function devedor()
    {
        return $this->belongsTo(Devedor::class);
    }

    public function historicoStatus()
    {
        return $this->hasMany(StatusCobranca::class);
    }
}
