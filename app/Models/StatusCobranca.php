<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusCobranca extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'status_cobranca';

    protected $fillable = [
        'tenant_id',
        'titulo_id',
        'status',
        'observacao'
    ];

    public function titulo()
    {
        return $this->belongsTo(Titulo::class);
    }
}
