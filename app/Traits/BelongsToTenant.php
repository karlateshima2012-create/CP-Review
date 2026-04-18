<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model) {
            if (! $model->tenant_id && app()->bound('tenant_id')) {
                $model->tenant_id = app('tenant_id');
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'tenant_id');
    }
}
