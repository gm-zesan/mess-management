<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Mess;

/**
 * Service for handling user deletion with role transfer and cleanup
 * Ensures managers cannot be deleted without transferring their role
 */
class UserDeletionService
{
    /**
     * Check if a user can be deleted and return validation status
     */
    public function canDelete(User $user): array
    {
        // Get all messes where this user is the manager
        $managedMesses = Mess::where('manager_id', $user->id)->get();

        if ($managedMesses->isNotEmpty()) {
            return [
                'allowed' => false,
                'reason' => 'manager',
                'message' => 'You cannot delete your account because you are the manager of ' . 
                            $managedMesses->count() . ' mess(es). Please transfer the manager role first.',
                'messes' => $managedMesses->map(fn($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                ])
            ];
        }

        return [
            'allowed' => true,
            'reason' => 'ok',
            'message' => 'User can be deleted'
        ];
    }

    /**
     * Get available users to transfer manager role to (from same messes)
     */
    public function getTransferCandidates(User $user)
    {
        // Get all messes where user has approved membership
        $messIds = $user->messUsers()
            ->where('status', 'approved')
            ->pluck('mess_id')
            ->toArray();

        if (empty($messIds)) {
            return collect();
        }

        // Get all other approved members in those messes
        return User::whereHas('messUsers', function ($query) use ($messIds) {
                $query->whereIn('mess_id', $messIds)
                    ->where('status', 'approved');
            })
            ->where('id', '!=', $user->id)
            ->with(['messUsers' => function ($query) use ($messIds) {
                $query->whereIn('mess_id', $messIds)
                    ->where('status', 'approved');
            }])
            ->orderBy('name')
            ->get();
    }

    /**
     * Transfer manager role from one user to another
     */
    public function transferManagerRole(User $fromUser, User $toUser, ?Mess $mess = null): bool
    {
        try {
            $managedMesses = Mess::where('manager_id', $fromUser->id);
            
            // If specific mess provided, only transfer for that mess
            if ($mess) {
                $managedMesses = $managedMesses->where('id', $mess->id);
            }

            $managedMesses->update([
                'manager_id' => $toUser->id
            ]);

            // Change role if this is the user's active mess
            if ($fromUser->activeMess()) {
                $fromUser->syncRoles([RoleEnum::MEMBER->value]);
            }

            if (!$toUser->hasRole(RoleEnum::MANAGER->value)) {
                $toUser->syncRoles([RoleEnum::MANAGER->value]);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Prepare user for deletion by cleaning up all associations
     */
    public function prepareForDeletion(User $user): void
    {
        // Remove from all messes
        $user->messUsers()->delete();
        $user->messes()->detach();

        // Remove from all roles
        $user->syncRoles([]);

        // Optionally reassign user-created data (expenses, meals, deposits)
        // These could be reassigned to a placeholder "deleted_user" or archived
        // For now, we keep the records (soft delete pattern)
    }

    /**
     * Delete user account with all cleanup
     */
    public function deleteUser(User $user): bool
    {
        try {
            // Prepare user first
            $this->prepareForDeletion($user);

            // Delete the user
            $user->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
