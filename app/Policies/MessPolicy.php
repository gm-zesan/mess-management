<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Mess;
use App\Models\MessUser;
use App\Models\User;

class MessPolicy
{
    /**
     * Determine if user can invite members to a mess
     */
    public function invite(User $user, Mess $mess): bool
    {
        // Check if user is an approved member of this mess
        return MessUser::where('mess_id', $mess->id)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Determine if user can approve members for a mess
     */
    public function approveMember(User $user, Mess $mess): bool
    {
        // Only managers can approve members
        return $user->hasRole(RoleEnum::MANAGER->value);
    }

    /**
     * Determine if user can view a mess
     */
    public function view(User $user, Mess $mess): bool
    {
        return MessUser::where('mess_id', $mess->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Determine if user can update a mess
     */
    public function update(User $user, Mess $mess): bool
    {
        return $user->hasRole(RoleEnum::MANAGER->value);
    }

    /**
     * Determine if user can delete a mess
     */
    public function delete(User $user, Mess $mess): bool
    {
        return $user->hasRole(RoleEnum::MANAGER->value);
    }
}
