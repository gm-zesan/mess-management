<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use App\Enums\RoleEnum;

/**
 * Automatic Global Scope for Tenant Isolation
 * This scope automatically filters all queries to only include records
 * belonging to the current user's active mess. This prevents accidental
 * cross-tenant data exposure
 * Applied to: Meal, Expense, Deposit, Month
 */
class MessTenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        // Skip scoping for unauthenticated requests
        if (!$user) {
            return;
        }

        // Superadmin can access all records (no filtering)
        if ($user->hasRole(RoleEnum::SUPERADMIN->value)) {
            return;
        }

        // Get user's active mess
        $activeMess = $user->messes()
            ->where('status', 'approved')
            ->first();

        // If user has an active mess, filter by it
        if ($activeMess) {
            $builder->where($model->getTable() . '.mess_id', $activeMess->id);
        } else {
            // User has no mess, return no results
            $builder->whereRaw('1=0');
        }
    }
}
