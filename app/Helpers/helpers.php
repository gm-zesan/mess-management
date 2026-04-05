<?php

use Illuminate\Support\Facades\Auth;
use App\Models\Mess;
use App\Enums\RoleEnum;
use App\Models\MessUser;

/**
 * Get the active mess for the current user.
 * For superadmin: returns the mess from session
 * For regular user: returns their approved mess
 * 
 * @return \App\Models\Mess|null
 */
function activeMess(): ?Mess
{
    if (!Auth::check()) {
        return null;
    }

    $user = Auth::user();
    
    // If superadmin, check session for entered mess
    if ($user->hasRole(RoleEnum::SUPERADMIN->value)) {
        $messId = session('superadmin_mess_id');
        if ($messId) {
            return Mess::find($messId);
        }
        return null;
    }
    
    // Get the user's first approved mess
    return $user->messes()
        ->where('status', 'approved')
        ->first();
}

/**
 * Get the active mess ID for the current user.
 * 
 * @return int|null
 */
function activeMessId(): ?int
{
    return activeMess()?->id;
}

/**
 * Get the active month ID for the current active mess.
 * 
 * @return int|null
 */
function activeMonthId(): ?int
{
    try {
        $activeMess = activeMess();
        if (!$activeMess) {
            return null;
        }
        return app(App\Services\MonthService::class)->getActiveMonth($activeMess->id)->id;
    } catch (\Exception $e) {
        return null;
    }
}

/**
 * Get the active month for the current active mess.
 * 
 * @return \App\Models\Month|null
 */
function activeMonth()
{
    try {
        $activeMess = activeMess();
        if (!$activeMess) {
            return null;
        }
        return app(App\Services\MonthService::class)->getActiveMonth($activeMess->id);
    } catch (\Exception $e) {
        return null;
    }
}

/**
 * Check if a month is closed.
 * 
 * @param int|\App\Models\Month $month
 * @return bool
 */
function isMonthClosed($month)
{
    return app(App\Services\MonthService::class)->isClosed($month);
}

/**
 * Get pending invitations count for the current user.
 * 
 * @return int
 */
function pendingInvitationsCount(): int
{
    if (!Auth::check()) {
        return 0;
    }

    $activeMess = Auth::user()->activeMess();

    if (isSuperAdminInMess()) {
        $activeMess = Mess::find(session('superadmin_mess_id'));
    }
    return MessUser::where('mess_id', $activeMess?->id)->where('status', 'pending')->count();
}

/**
 * Get all pending invitations for the current user.
 * 
 * @return \Illuminate\Database\Eloquent\Collection
 */
function getPendingInvitations()
{
    if (!Auth::check()) {
        return collect();
    }

    return Auth::user()->messUsers()
        ->where('status', 'pending')
        ->with('mess')
        ->get();
}

/**
 * Check if superadmin is currently in a mess (via session).
 * 
 * @return bool
 */
function isSuperAdminInMess(): bool
{
    if (!Auth::check()) {
        return false;
    }

    $user = Auth::user();
    
    // Check if user is superadmin and has a mess in session
    if ($user->hasRole(RoleEnum::SUPERADMIN->value)) {
        return session()->has('superadmin_mess_id');
    }
    
    return false;
}
