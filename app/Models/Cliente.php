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
        'endereco',
        'pix_chave',
        'max_parcelas_cartao',
        'multa_percentual',
        'juros_mensal',
        'honorarios_percentual',
        'ipca_mensal',
    ];

    protected $casts = [
        'max_parcelas_cartao'   => 'integer',
        'multa_percentual'      => 'decimal:2',
        'juros_mensal'          => 'decimal:2',
        'honorarios_percentual' => 'decimal:2',
        'ipca_mensal'           => 'decimal:2',
    ];

    /**
     * Relacionamento com devedores
     */
    public function devedores()
    {
        return $this->hasMany(Devedor::class);
    }
}
