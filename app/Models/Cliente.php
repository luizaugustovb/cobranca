<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'nome',
        'documento',
        'email',
        'telefone',
        'endereco'
    ];

    /**
     * Relacionamento com devedores
     */
    public function devedores()
    {
        return $this->hasMany(Devedor::class);
    }
}
