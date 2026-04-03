<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\PermissionEnum;

class UserPolicy
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
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::MEMBERS_VIEW->value);
    }

    /**
     * Determine whether the user can view the user.
     */
    public function view(User $user, User $model): bool
    {
        return $user->can(PermissionEnum::MEMBERS_VIEW->value);
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::MEMBERS_CREATE->value);
    }

    /**
     * Determine whether the user can update the user.
     */
    public function update(User $user, User $model): bool
    {
        return $user->can(PermissionEnum::MEMBERS_EDIT->value);
    }

    /**
     * Determine whether the user can delete the user.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->can(PermissionEnum::MEMBERS_DELETE->value);
    }

    /**
     * Determine whether the user can restore the user.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->can(PermissionEnum::MEMBERS_EDIT->value);
    }

    /**
     * Determine whether the user can permanently delete the user.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->can(PermissionEnum::MEMBERS_DELETE->value);
    }

    /**
     * Determine whether the user can manage roles.
     */
    public function manageRoles(User $user): bool
    {
        return $user->can(PermissionEnum::MEMBERS_MANAGE_ROLES->value);
    }
}
