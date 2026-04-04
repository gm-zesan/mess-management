<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Models\Deposit;
use App\Models\User;

class DepositController extends Controller
{
    /**
     * Display a listing of the deposits.
     */
    public function index()
    {
        $this->authorize('viewAny', Deposit::class);
        
        $activeMess = activeMess();
        $activeMonth = activeMonth();
        
        if (!$activeMess || !$activeMonth) {
            return redirect(route('dashboard'))->with('error', 'No active mess or month found.');
        }
        
        $deposits = Deposit::with(['user', 'month', 'mess'])
            ->where('mess_id', $activeMess->id)
            ->where('month_id', $activeMonth->id)
            ->latest('date')
            ->paginate(15);

        return view('deposits.index', compact('deposits', 'activeMess', 'activeMonth'));
    }

    /**
     * Show the form for creating a new deposit.
     */
    public function create()
    {
        $this->authorize('create', Deposit::class);
        
        $members = User::get();
        $activeMonth = activeMonth();

        return view('deposits.create', compact('members', 'activeMonth'));
    }

    /**
     * Store a newly created deposit in storage.
     */
    public function store(StoreDepositRequest $request)
    {
        $this->authorize('create', Deposit::class);
        
        $data = $request->validated();
        
        // Auto-assign active mess and month
        $activeMess = activeMess();
        $activeMonth = activeMonth();
        
        if (!$activeMess || !$activeMonth) {
            return redirect()->back()
                ->with('error', 'No active mess or month found.');
        }
        
        // Check if month is closed
        if (isMonthClosed($activeMonth)) {
            return redirect()->back()
                ->with('error', 'This month is closed. No further modifications are allowed.');
        }
        
        $data['mess_id'] = $activeMess->id;
        $data['month_id'] = $activeMonth->id;
        Deposit::create($data);

        return redirect()->route('deposits.index')
            ->with('success', 'Deposit record created successfully.');
    }

    /**
     * Show the form for editing the specified deposit.
     */
    public function edit(Deposit $deposit)
    {
        $this->authorize('update', $deposit);
        
        $members = User::get();
        $activeMonth = activeMonth();

        return view('deposits.edit', compact('deposit', 'members', 'activeMonth'));
    }

    /**
     * Update the specified deposit in storage.
     */
    public function update(StoreDepositRequest $request, Deposit $deposit)
    {
        $this->authorize('update', $deposit);
        
        // Check if month is closed
        if (isMonthClosed($deposit->month_id)) {
            return redirect()->back()
                ->with('error', 'This month is closed. No further modifications are allowed.');
        }
        
        $data = $request->validated();
        
        // Keep month_id as the active month
        $activeMonth = activeMonth();
        $data['month_id'] = $activeMonth->id;
        
        $deposit->update($data);

        return redirect()->route('deposits.index')
            ->with('success', 'Deposit record updated successfully.');
    }

    /**
     * Remove the specified deposit from storage.
     */
    public function destroy(Deposit $deposit)
    {
        $this->authorize('delete', $deposit);
        
        // Check if month is closed
        if (isMonthClosed($deposit->month_id)) {
            return redirect()->back()
                ->with('error', 'This month is closed. No further deletions are allowed.');
        }
        
        $deposit->delete();

        return redirect()->route('deposits.index')
            ->with('success', 'Deposit record deleted successfully.');
    }
}
