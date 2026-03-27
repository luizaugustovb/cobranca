<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportacaoItem extends Model
{
    use HasFactory;

    protected $table = 'importacao_itens';

    protected $fillable = [
        'importacao_id',
        'linha',
        'dados',
        'status',
        'erros'
    ];

    protected $casts = [
        'dados' => 'array',
    ];

    public function importacao()
    {
        return $this->belongsTo(Importacao::class, 'importacao_id');
    }
}
