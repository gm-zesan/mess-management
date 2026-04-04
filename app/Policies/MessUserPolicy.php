<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\MessUser;
use App\Models\User;

class MessUserPolicy
{
    /**
     * Determine if user can update a mess user record
     */
    public function update(User $user, MessUser $messUser): bool
    {
        // Only managers can update mess user records
        return $user->hasRole(RoleEnum::MANAGER->value) && 
               $messUser->mess_id === $messUser->mess_id;
    }

    /**
     * Determine if user can delete a mess user record
     */
    public function delete(User $user, MessUser $messUser): bool
    {
        return $user->hasRole(RoleEnum::MANAGER->value);
    }
}
