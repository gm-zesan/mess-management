<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Models\Deposit;
use App\Models\Member;

class DepositController extends Controller
{
    /**
     * Display a listing of the deposits.
     */
    public function index()
    {
        $deposits = Deposit::with(['member', 'month'])
            ->latest('date')
            ->paginate(15);

        return view('deposits.index', compact('deposits'));
    }

    /**
     * Show the form for creating a new deposit.
     */
    public function create()
    {
        $members = Member::where('status', 'active')->get();
        $activeMonth = activeMonth();

        return view('deposits.create', compact('members', 'activeMonth'));
    }

    /**
     * Store a newly created deposit in storage.
     */
    public function store(StoreDepositRequest $request)
    {
        $data = $request->validated();
        
        // Auto-assign active month
        $activeMonth = activeMonth();
        
        // Check if month is closed
        if (isMonthClosed($activeMonth)) {
            return redirect()->back()
                ->with('error', 'This month is closed. No further modifications are allowed.');
        }
        
        $data['month_id'] = $activeMonth->id;
        Deposit::create($data);

        return redirect()->route('deposits.index')
            ->with('success', 'Deposit record created successfully.');
    }

    /**
     * Display the specified deposit.
     */
    public function show(Deposit $deposit)
    {
        return view('deposits.show', compact('deposit'));
    }

    /**
     * Show the form for editing the specified deposit.
     */
    public function edit(Deposit $deposit)
    {
        $members = Member::where('status', 'active')->get();
        $activeMonth = activeMonth();

        return view('deposits.edit', compact('deposit', 'members', 'activeMonth'));
    }

    /**
     * Update the specified deposit in storage.
     */
    public function update(StoreDepositRequest $request, Deposit $deposit)
    {
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
