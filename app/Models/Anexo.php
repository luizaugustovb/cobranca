<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'anexavel_id',
        'anexavel_type',
        'nome',
        'caminho',
        'tipo',
        'tamanho'
    ];

    public function anexavel()
    {
        return $this->morphTo();
    }
}
