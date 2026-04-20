<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Detecção dinâmica da coluna para evitar crash em tabelas legado
        $column = \Illuminate\Support\Facades\Schema::hasColumn($model->getTable(), 'tenant_id') 
                  ? 'tenant_id' : 'cliente_id';

        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->role === 'super_admin' && session()->has('impersonate_tenant_id')) {
                $builder->where($model->getTable() . '.' . $column, session('impersonate_tenant_id'));
            } 
            else if ($user->role !== 'super_admin' && $user->tenant_id) {
                $builder->where($model->getTable() . '.' . $column, $user->tenant_id);
            }
        }
        
        if (app()->bound('tenant_id')) {
            $builder->where($model->getTable() . '.' . $column, app('tenant_id'));
        }
    }
}
