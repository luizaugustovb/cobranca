<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devedor extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'devedores';

    protected $fillable = [
        'tenant_id',
        'cliente_id',
        'nome',
        'cpf_cnpj',
        'email',
        'telefone',
        'rua',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'asaas_customer_id',
    ];

    /**
     * Relacionamento com cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relacionamento com aluno
     */
    public function aluno()
    {
        return $this->hasOne(Aluno::class);
    }

    /**
     * Relacionamento com titulos
     */
    public function titulos()
    {
        return $this->hasMany(Titulo::class);
    }

    /**
     * Relacionamento com acordos
     */
    public function acordos()
    {
        return $this->hasMany(Acordo::class);
    }

    /**
     * Relacionamento com historico de contatos
     */
    public function contatos()
    {
        return $this->hasMany(HistoricoContato::class);
    }
}
