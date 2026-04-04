<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Mess;
use App\Models\MessUser;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MessSelectionController extends Controller
{
    /**
     * Display mess selection page
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();
        
        // If user already has an approved mess, redirect to dashboard
        if ($user->activeMess()) {
            return redirect(route('dashboard'));
        }
        
        $userMessIds = $user->messUsers()->pluck('mess_id')->toArray();
        $availableMesses = Mess::whereNotIn('id', $userMessIds)->get();

        return view('mess.selection', [
            'availableMesses' => $availableMesses,
        ]);
    }

    /**
     * Create a new mess
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        // Check if user already has an approved mess
        $existingMess = MessUser::where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();

        if ($existingMess) {
            return redirect(route('mess.selection'))->with('error', 'You can only be a member of one mess at a time.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:messes',
            'description' => 'nullable|string|max:1000',
        ]);

        // Generate unique join code
        $joinCode = $this->generateUniqueJoinCode();

        // Create the mess
        $mess = Mess::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'creator_id' => $user->id,
            'manager_id' => $user->id,  // User is the manager of their own mess
            'join_code' => $joinCode,
        ]);

        // Add creator to mess_user with approved status
        MessUser::create([
            'mess_id' => $mess->id,
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        // Assign manager role using Spatie
        $user->assignRole(RoleEnum::MANAGER->value);

        return redirect(route('dashboard'))->with('success', "Mess '{$mess->name}' created successfully!");
    }

    /**
     * Generate a unique join code for the mess
     */
    private function generateUniqueJoinCode(): string
    {
        do {
            $joinCode = strtoupper(Str::random(8));
        } while (Mess::where('join_code', $joinCode)->exists());

        return $joinCode;
    }

    /**
     * Join an existing mess
     */
    public function join(Request $request, Mess $mess)
    {
        $user = Auth::user();

        // Check if already a member of this mess
        if (MessUser::where('mess_id', $mess->id)->where('user_id', $user->id)->exists()) {
            return redirect(route('mess.selection'))->with('error', 'You are already a member of this mess.');
        }

        // Check if user already has an approved mess membership
        $existingMess = MessUser::where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();

        if ($existingMess) {
            return redirect(route('mess.selection'))->with('error', 'You can only be a member of one mess at a time.');
        }

        // Add user as pending member
        MessUser::create([
            'mess_id' => $mess->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        return redirect(route('mess.selection'))->with('success', 'Request to join sent! Waiting for admin approval.');
    }

    /**
     * Show pending users waiting for approval in the current mess
     */
    public function pendingInvitations(): View
    {
        $user = Auth::user();
        $activeMess = $user->activeMess();

        if (!$activeMess) {
            return redirect(route('mess.selection'))->with('error', 'Please select a mess first.');
        }

        // Get only pending users in the current mess
        $pendingUsers = MessUser::where('mess_id', $activeMess->id)
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('mess.pending-invitations', [
            'pendingUsers' => $pendingUsers,
            'activeMess' => $activeMess,
        ]);
    }

    /**
     * Approve a pending user for the mess
     */
    public function approveUser(MessUser $messUser)
    {
        $user = Auth::user();
        $activeMess = $user->activeMess();

        // Verify user is manager of this mess
        if (!$activeMess || $messUser->mess_id !== $activeMess->id) {
            abort(403, 'Unauthorized');
        }

        // Only manager can approve
        if ($activeMess->manager_id !== $user->id) {
            abort(403, 'Only mess manager can approve users');
        }

        $messUser->update(['status' => 'approved']);

        // Assign MEMBER role to the approved user
        $messUser->user->assignRole(RoleEnum::MEMBER->value);

        return redirect(route('dashboard'))->with('success', $messUser->user->name . ' has been approved!');
    }

    /**
     * Reject a pending user for the mess
     */
    public function rejectUser(MessUser $messUser)
    {
        $user = Auth::user();
        $activeMess = $user->activeMess();

        // Verify user is manager of this mess
        if (!$activeMess || $messUser->mess_id !== $activeMess->id) {
            abort(403, 'Unauthorized');
        }

        // Only manager can reject
        if ($activeMess->manager_id !== $user->id) {
            abort(403, 'Only mess manager can reject users');
        }

        $messUser->delete();

        return redirect(route('mess.pending-invitations'))->with('success', 'User request has been rejected.');
    }
}
