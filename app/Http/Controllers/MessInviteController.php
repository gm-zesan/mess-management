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
     * View pending members for approval
     */
    public function pending(Mess $mess): View
    {
        $this->authorize('approveMember', $mess);

        $pendingMembers = $mess->messUsers()
            ->where('status', 'pending')
            ->with('user')
            ->get();

        return view('mess.pending-members', [
            'mess' => $mess,
            'pendingMembers' => $pendingMembers,
        ]);
    }

    /**
     * Approve a pending member
     */
    public function approve(Mess $mess, MessUser $messUser)
    {
        $this->authorize('approveMember', $mess);

        // Ensure the mess_user belongs to this mess
        $this->authorize('update', $messUser);

        $messUser->update(['status' => 'approved']);

        // Assign member role to the user
        if (!$messUser->user->hasRole(RoleEnum::MEMBER->value)) {
            $messUser->user->assignRole(RoleEnum::MEMBER->value);
        }

        return redirect(route('mess.pending', $mess))->with('success', "Member {$messUser->user->name} approved!");
    }

    /**
     * Reject a pending member
     */
    public function reject(Mess $mess, MessUser $messUser)
    {
        $this->authorize('approveMember', $mess);

        // Ensure the mess_user belongs to this mess
        $this->authorize('update', $messUser);

        $messUser->update(['status' => 'rejected']);

        return redirect(route('mess.pending', $mess))->with('success', "Member {$messUser->user->name} rejected!");
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
}
