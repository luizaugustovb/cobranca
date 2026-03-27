<?php

namespace App\Traits;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToTenant(): void
    {
        // Aplica o escopo global automaticamente
        static::addGlobalScope(new TenantScope());

        // Seta o tenant_id automaticamente ao criar o registro se não for informado
        static::creating(function (Model $model) {
            if (session('tenant_id') && !$model->tenant_id) {
                $model->tenant_id = session('tenant_id');
            }
        });
    }

    /**
     * Relacionamento com o Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}
