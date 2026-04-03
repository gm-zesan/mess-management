<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Month;
use App\Enums\PermissionEnum;

class MonthPolicy
{
    /**
     * Perform pre-authorization checks on the model.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any months.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::MONTHS_VIEW->value);
    }

    /**
     * Determine whether the user can view the month.
     */
    public function view(User $user, Month $month): bool
    {
        return $user->can(PermissionEnum::MONTHS_VIEW->value);
    }

    /**
     * Determine whether the user can create months.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::MONTHS_CREATE->value);
    }

    /**
     * Determine whether the user can update the month.
     */
    public function update(User $user, Month $month): bool
    {
        return $user->can(PermissionEnum::MONTHS_EDIT->value);
    }

    /**
     * Determine whether the user can delete the month.
     */
    public function delete(User $user, Month $month): bool
    {
        return $user->can(PermissionEnum::MONTHS_DELETE->value);
    }

    /**
     * Determine whether the user can close the month.
     */
    public function close(User $user, Month $month): bool
    {
        return $user->can(PermissionEnum::MONTHS_CLOSE->value);
    }

    /**
     * Determine whether the user can restore the month.
     */
    public function restore(User $user, Month $month): bool
    {
        return $user->can(PermissionEnum::MONTHS_EDIT->value);
    }

    /**
     * Determine whether the user can permanently delete the month.
     */
    public function forceDelete(User $user, Month $month): bool
    {
        return $user->can(PermissionEnum::MONTHS_DELETE->value);
    }
}
