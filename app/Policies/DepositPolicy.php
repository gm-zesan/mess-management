<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Deposit;
use App\Enums\PermissionEnum;

class DepositPolicy
{
    /**
     * Perform pre-authorization checks on the model.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole(RoleEnum::SUPERADMIN->value)) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any deposits.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::DEPOSITS_VIEW->value);
    }

    /**
     * Determine whether the user can view the deposit.
     */
    public function view(User $user, Deposit $deposit): bool
    {
        return $user->can(PermissionEnum::DEPOSITS_VIEW->value);
    }

    /**
     * Determine whether the user can create deposits.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::DEPOSITS_CREATE->value);
    }

    /**
     * Determine whether the user can update the deposit.
     */
    public function update(User $user, Deposit $deposit): bool
    {
        return $user->can(PermissionEnum::DEPOSITS_EDIT->value);
    }

    /**
     * Determine whether the user can delete the deposit.
     */
    public function delete(User $user, Deposit $deposit): bool
    {
        return $user->can(PermissionEnum::DEPOSITS_DELETE->value);
    }

    /**
     * Determine whether the user can restore the deposit.
     */
    public function restore(User $user, Deposit $deposit): bool
    {
        return $user->can(PermissionEnum::DEPOSITS_EDIT->value);
    }

    /**
     * Determine whether the user can permanently delete the deposit.
     */
    public function forceDelete(User $user, Deposit $deposit): bool
    {
        return $user->can(PermissionEnum::DEPOSITS_DELETE->value);
    }
}
