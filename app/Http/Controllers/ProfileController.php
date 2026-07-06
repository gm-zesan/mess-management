<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\UserDeletionService;
use App\Models\User;
use App\Models\Mess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account with proper role transfer and cleanup.
     */
    public function destroy(Request $request, UserDeletionService $deletionService): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Check if user can be deleted
        $canDelete = $deletionService->canDelete($user);
        
        if (!$canDelete['allowed']) {
            return Redirect::route('profile.edit')
                ->with('error', $canDelete['message'])
                ->withBag('userDeletion');
        }

        // Delete user FIRST, then logout
        $deleted = $deletionService->deleteUser($user);
        
        if (!$deleted) {
            return Redirect::route('profile.edit')
                ->with('error', 'Failed to delete account. Please try again.')
                ->withBag('userDeletion');
        }

        // Then logout and invalidate session
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('status', 'profile-deleted');
    }

    /**
     * Check if user can delete their account
     */
    public function checkDeletionEligibility(Request $request, UserDeletionService $deletionService)
    {
        $canDelete = $deletionService->canDelete($request->user());

        if (!$canDelete['allowed'] && $canDelete['reason'] === 'manager') {
            $candidates = $deletionService->getTransferCandidates($request->user());
            return response()->json([
                'allowed' => false,
                'reason' => 'manager',
                'message' => $canDelete['message'],
                'messes' => $canDelete['messes'],
                'candidates' => $candidates->map(fn($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ])->toArray(),
            ]);
        }

        return response()->json([
            'allowed' => true,
            'reason' => 'ok',
            'message' => 'User can delete their account',
        ]);
    }

    /**
     * Transfer manager role to other users before deletion
     */
    public function transferManagerRole(Request $request, UserDeletionService $deletionService)
    {
        $user = $request->user();
        $transfers = $request->input('transfers', []);

        try {
            foreach ($transfers as $transfer) {
                $toUser = User::find($transfer['new_manager_id']);
                $mess = Mess::find($transfer['mess_id']);

                if (!$toUser || !$mess) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid user or mess data'
                    ], 422);
                }

                $deletionService->transferManagerRole($user, $toUser, $mess);
            }

            return response()->json([
                'success' => true,
                'message' => 'Manager roles transferred successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error transferring manager roles: ' . $e->getMessage()
            ], 500);
        }
    }
}
