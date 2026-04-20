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
        // If we have a logged in user who is NOT a super admin, 
        // filter by their tenant_id
        if (Auth::check()) {
            $user = Auth::user();
            
            // Suporte para Impersonate (Super Admin simulando um Lojista)
            if ($user->role === 'super_admin' && session()->has('impersonate_tenant_id')) {
                $builder->where($model->getTable() . '.tenant_id', session('impersonate_tenant_id'));
            } 
            // Filtro normal para Lojistas
            else if ($user->role !== 'super_admin' && $user->tenant_id) {
                $builder->where($model->getTable() . '.tenant_id', $user->tenant_id);
            }
        }
        
        // Also check if there's a specific tenant context (e.g. from middleware)
        if (app()->bound('tenant_id')) {
            $builder->where($model->getTable() . '.tenant_id', app('tenant_id'));
        }
    }
}
