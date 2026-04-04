<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    /**
     * Determine whether the user can view permissions.
     */
    public function view(User $user): bool
    {
        return $user->hasRole(RoleEnum::SUPERADMIN->value);
    }

    /**
     * Determine whether the user can manage permissions.
     */
    public function manage(User $user): bool
    {
        return $user->hasRole(RoleEnum::SUPERADMIN->value);
    }
}
