<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoContato extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'historico_contatos';

    protected $fillable = [
        'tenant_id',
        'devedor_id',
        'tipo',
        'canal',
        'descricao',
        'resultado'
    ];

    public function devedor()
    {
        return $this->belongsTo(Devedor::class);
    }
}
