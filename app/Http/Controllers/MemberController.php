<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\RoleEnum;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        
        $activeMess = activeMess();
        
        if (!$activeMess) {
            return redirect()->route('mess.selection')->with('error', 'Please select a mess first.');
        }
        
        // Get members of the current mess with roles
        $members = $activeMess->messUsers()
            ->where('status', 'approved')
            ->with('user.roles')
            ->paginate(10);
        
        return view('members.index', compact('members', 'activeMess'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $this->authorize('create', User::class);
        
        User::create($request->validated());
        return redirect()->route('members.index')->with('success', 'Member created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $member)
    {
        $this->authorize('update', $member);
        
        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, User $member)
    {
        $this->authorize('update', $member);
        
        $member->update($request->validated());
        return redirect()->route('members.show', $member)->with('success', 'Member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $member)
    {
        $this->authorize('delete', $member);
        
        $member->delete();
        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }

    /**
     * Change manager - Assign manager role to selected user and remove from others
     */
    public function changeManager(User $member)
    {
        // Check permission
        $this->authorize('update', $member);
        
        /** @var User $user */
        $user = Auth::user();
        if (!$user->can('members.manage-roles')) {
            abort(403);
        }

        // Get current managers and assign them the member role
        User::role(RoleEnum::MANAGER->value)->get()->each(function ($user) {
            $user->syncRoles([RoleEnum::MEMBER->value]);
        });

        // Assign manager role to selected user
        $member->syncRoles([RoleEnum::MANAGER->value]);

        return redirect()->route('members.index')->with('success', "{$member->name} is now the manager.");
    
    }
}
