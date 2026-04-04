<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Mess;
use App\Models\MessUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessInviteController extends Controller
{
    /**
     * Show invite form for a mess
     */
    public function create(Mess $mess): View
    {
        $this->authorize('invite', $mess);

        return view('mess.invite', [
            'mess' => $mess,
        ]);
    }

    /**
     * Send invitation to a user
     */
    public function store(Request $request, Mess $mess)
    {
        $this->authorize('invite', $mess);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $invitee = User::where('email', $validated['email'])->first();

        // Check if user is already a member of this mess
        if (MessUser::where('mess_id', $mess->id)->where('user_id', $invitee->id)->exists()) {
            return redirect()->back()->with('error', 'User is already a member of this mess.');
        }

        // Check if user already has an approved mess membership
        $existingMess = MessUser::where('user_id', $invitee->id)
            ->where('status', 'approved')
            ->first();

        if ($existingMess) {
            return redirect()->back()->with('error', "User {$invitee->email} is already a member of another mess.");
        }

        // Create pending membership
        MessUser::create([
            'mess_id' => $mess->id,
            'user_id' => $invitee->id,
            'status' => 'pending',
            'invited_by_id' => Auth::id(),
        ]);

        return redirect(route('mess.members', $mess))->with('success', "Invitation sent to {$invitee->email}!");
    }


    /**
     * View all members of a mess
     */
    public function members(Mess $mess): View
    {
        $this->authorize('view', $mess);

        $approvedMembers = $mess->messUsers()
            ->where('status', 'approved')
            ->with('user')
            ->get();

        return view('mess.members', [
            'mess' => $mess,
            'members' => $approvedMembers,
        ]);
    }

    /**
     * Show mess profile
     */
    public function profile(Mess $mess): View
    {
        $this->authorize('view', $mess);

        return view('mess.profile', [
            'mess' => $mess,
        ]);
    }

    /**
     * Update mess profile
     */
    public function updateProfile(Request $request, Mess $mess)
    {
        $this->authorize('update', $mess);

        $user = Auth::user();

        // Superadmin can edit everything, manager can only edit description
        if ($user->hasRole(RoleEnum::SUPERADMIN->value)) {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:messes,name,' . $mess->id,
                'description' => 'nullable|string|max:1000',
            ]);
            
            $mess->update($validated);
        } else {
            // Manager can only edit description
            $validated = $request->validate([
                'description' => 'nullable|string|max:1000',
            ]);
            
            $mess->update($validated);
        }

        return redirect(route('mess.profile', $mess))->with('success', 'Mess profile updated successfully!');
    }
}

