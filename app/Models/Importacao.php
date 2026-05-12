<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Importacao extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'importacoes';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'cliente_id',
        'arquivo',
        'tipo',
        'status',
        'total',
        'processados',
        'erros'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itens()
    {
        return $this->hasMany(ImportacaoItem::class, 'importacao_id');
    }
}
