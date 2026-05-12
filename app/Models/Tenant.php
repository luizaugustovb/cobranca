<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'document',
        'email',
        'phone',
        'logo',
        'status',
        'plan',
        'settings',
        'viicio_token',
        'viicio_company_id',
        'whatsapp_ativo',
    ];

    protected $casts = [
        'settings'       => 'array',
        'whatsapp_ativo' => 'boolean',
    ];

    /**
     * Relacionamento com usuários
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
