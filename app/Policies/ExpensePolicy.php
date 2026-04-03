<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Expense;
use App\Enums\PermissionEnum;

class ExpensePolicy
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
     * Determine whether the user can view any expenses.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::EXPENSES_VIEW->value);
    }

    /**
     * Determine whether the user can view the expense.
     */
    public function view(User $user, Expense $expense): bool
    {
        return $user->can(PermissionEnum::EXPENSES_VIEW->value);
    }

    /**
     * Determine whether the user can create expenses.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::EXPENSES_CREATE->value);
    }

    /**
     * Determine whether the user can update the expense.
     */
    public function update(User $user, Expense $expense): bool
    {
        return $user->can(PermissionEnum::EXPENSES_EDIT->value);
    }

    /**
     * Determine whether the user can delete the expense.
     */
    public function delete(User $user, Expense $expense): bool
    {
        return $user->can(PermissionEnum::EXPENSES_DELETE->value);
    }

    /**
     * Determine whether the user can restore the expense.
     */
    public function restore(User $user, Expense $expense): bool
    {
        return $user->can(PermissionEnum::EXPENSES_EDIT->value);
    }

    /**
     * Determine whether the user can permanently delete the expense.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        return $user->can(PermissionEnum::EXPENSES_DELETE->value);
    }
}
