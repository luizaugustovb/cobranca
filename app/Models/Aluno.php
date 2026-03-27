<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'devedor_id',
        'nome',
        'matricula',
        'curso'
    ];

    public function devedor()
    {
        return $this->belongsTo(Devedor::class);
    }
}
