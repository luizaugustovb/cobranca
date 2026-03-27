<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Só aplica se houver um tenant selecionado (ex: na sessão ou no request)
        // Admin geral vê tudo (pode ser checado aqui ou no trait)
        if (session('tenant_id')) {
            if ($model instanceof \App\Models\User) {
                $builder->where(function ($query) use ($model) {
                    $query->where($model->getTable() . '.tenant_id', session('tenant_id'))
                          ->orWhereNull($model->getTable() . '.tenant_id');
                });
            } else {
                $builder->where($model->getTable() . '.tenant_id', session('tenant_id'));
            }
        }
    }
}
