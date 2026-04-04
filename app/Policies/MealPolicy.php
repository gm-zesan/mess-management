<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Meal;
use App\Enums\PermissionEnum;

class MealPolicy
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
     * Determine whether the user can view any meals.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::MEALS_VIEW->value);
    }

    /**
     * Determine whether the user can view the meal.
     */
    public function view(User $user, Meal $meal): bool
    {
        return $user->can(PermissionEnum::MEALS_VIEW->value);
    }

    /**
     * Determine whether the user can create meals.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::MEALS_CREATE->value);
    }

    /**
     * Determine whether the user can update the meal.
     */
    public function update(User $user, Meal $meal): bool
    {
        return $user->can(PermissionEnum::MEALS_EDIT->value);
    }

    /**
     * Determine whether the user can delete the meal.
     */
    public function delete(User $user, Meal $meal): bool
    {
        return $user->can(PermissionEnum::MEALS_DELETE->value);
    }

    /**
     * Determine whether the user can restore the meal.
     */
    public function restore(User $user, Meal $meal): bool
    {
        return $user->can(PermissionEnum::MEALS_EDIT->value);
    }

    /**
     * Determine whether the user can permanently delete the meal.
     */
    public function forceDelete(User $user, Meal $meal): bool
    {
        return $user->can(PermissionEnum::MEALS_DELETE->value);
    }
}
