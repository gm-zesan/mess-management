<?php

use Illuminate\Support\Facades\Auth;
use App\Models\Mess;

/**
 * Get the active mess for the current user.
 * 
 * @return \App\Models\Mess|null
 */
function activeMess(): ?Mess
{
    if (!Auth::check()) {
        return null;
    }

    $user = Auth::user();
    
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
 * Get the active month ID.
 * 
 * @return int|null
 */
function activeMonthId()
{
    try {
        return app(App\Services\MonthService::class)->getActiveMonth()->id;
    } catch (\Exception $e) {
        return null;
    }
}

/**
 * Get the active month.
 * 
 * @return \App\Models\Month|null
 */
function activeMonth()
{
    try {
        return app(App\Services\MonthService::class)->getActiveMonth();
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
