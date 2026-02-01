<?php

declare(strict_types=1);

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * TenantScope - Provides automatic tenant filtering for multi-tenancy.
 *
 * This scope automatically filters queries to only return records
 * belonging to the current tenant. If no tenant is set (e.g., in
 * admin panel context), the scope is not applied.
 *
 * Usage: Add `static::addGlobalScope(new TenantScope)` in model's booted().
 * Bypass: Use `Model::withoutGlobalScope(TenantScope::class)` or
 *         `Model::withoutGlobalScopes()` for admin queries.
 */
class TenantScope implements Scope
{
    /**
     * Apply the tenant scope to the query.
     *
     * Only applies filtering when a current_tenant is bound in the container.
     * Admin panel and CLI contexts will not have a tenant set.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Only apply scope if current_tenant is bound in container
        if (! app()->bound('current_tenant')) {
            return;
        }

        $tenant = app('current_tenant');

        if ($tenant?->id) {
            $builder->where($model->getTable() . '.tenant_id', $tenant->id);
        }
    }
}
