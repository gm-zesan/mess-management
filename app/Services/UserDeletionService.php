<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Mess;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Service for handling user deletion with role transfer and cleanup
 * Ensures managers cannot be deleted without transferring their role
 */
class UserDeletionService
{
    /**
     * Check if a user can be deleted and return validation status
     * 
     * Logic:
     * - User CANNOT delete while joined to ANY mess (active membership)
     * - User CAN delete after leaving all messes (permanent hard delete)
     * - User CANNOT delete if they are manager of any mess
     */
    public function canDelete(User $user): array
    {
        // Check if user is still joined to any mess (approved or pending membership)
        $activeMemberships = $user->messUsers()
            ->whereIn('status', ['approved', 'pending'])
            ->exists();

        if ($activeMemberships) {
            return [
                'allowed' => false,
                'reason' => 'still_joined',
                'message' => 'You cannot delete your account while joined to a mess. Please leave all messes first.',
            ];
        }

        // Get all messes where this user is the manager (shouldn't happen if they left, but double-check)
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

        // Can be permanently deleted
        return [
            'allowed' => true,
            'reason' => 'ok',
            'message' => 'Your account will be permanently deleted'
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
     * Prepare user for deletion by cleaning up associations
     * 
     * This method:
     * - REMOVES: mess memberships, role assignments, all financial records
     * - WRAPPED: In database transaction for atomicity
     */
    public function prepareForDeletion(User $user): void
    {
        try {
            DB::transaction(function() use ($user) {
                // Delete all financial records
                $user->expenses()->delete();
                $user->deposits()->delete();
                $user->meals()->delete();

                // Detach from all messes
                $user->messes()->detach();
                $user->messUsers()->delete();

                // Remove from all roles
                $user->syncRoles([]);
                
                Log::info('User ' . $user->id . ' prepared for permanent deletion.');
            });
        } catch (\Exception $e) {
            Log::error('Error preparing user for deletion: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Permanently delete user account (hard delete)
     * 
     * Deletes:
     * - User record
     * - All financial records (expenses, deposits, meals)
     * - All mess memberships
     * - All roles and permissions
     * 
     * WRAPPED: In database transaction for atomicity
     */
    public function deleteUser(User $user): bool
    {
        try {
            DB::transaction(function() use ($user) {
                // Prepare user first (remove all associations and financial records)
                $this->prepareForDeletion($user);

                // Hard delete the user permanently
                $user->forceDelete();

                Log::info('User ' . $user->id . ' (' . $user->email . ') permanently deleted.');
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return false;
        }
    }
}
