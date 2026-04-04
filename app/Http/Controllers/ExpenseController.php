<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Models\User;
use App\Models\Deposit;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     */
    public function index()
    {
        $this->authorize('viewAny', Expense::class);
        
        $activeMess = activeMess();
        $activeMonth = activeMonth();
        
        if (!$activeMess || !$activeMonth) {
            return redirect(route('dashboard'))->with('error', 'No active mess or month found.');
        }
        
        $expenses = Expense::with('month', 'user', 'mess')
            ->where('mess_id', $activeMess->id)
            ->where('month_id', $activeMonth->id)
            ->latest('date')
            ->paginate(15);

        return view('expenses.index', compact('expenses', 'activeMess', 'activeMonth'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $this->authorize('create', Expense::class);
        
        $activeMess = activeMess();
        $activeMonth = activeMonth();
        
        if (!$activeMess || !$activeMonth) {
            return redirect(route('dashboard'))->with('error', 'No active mess or month found.');
        }
        
        // Get only approved members of the active mess
        $members = $activeMess->approvedMembers()->orderBy('name')->get();

        return view('expenses.create', compact('activeMonth', 'members', 'activeMess'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(StoreExpenseRequest $request)
    {
        $this->authorize('create', Expense::class);
        
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
        Expense::create($data);

        // Check if also creating a deposit
        if ($request->has('with_deposit') && $request->input('with_deposit') == 1) {
            $user_id = $request->input('user_id');
            
            // Only create deposit if user_id is provided
            if ($user_id) {
                Deposit::create([
                    'mess_id' => $activeMess->id,
                    'user_id' => $user_id,
                    'month_id' => $activeMonth->id,
                    'amount' => $data['amount'],
                    'date' => $data['date'],
                ]);
            }
        }

        $message = ($request->has('with_deposit') && $request->input('with_deposit') == 1)
            ? 'Expense and deposit created successfully.'
            : 'Expense record created successfully.';

        return redirect()->route('expenses.index')
            ->with('success', $message);
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense);
        
        $activeMess = activeMess();
        $activeMonth = activeMonth();
        
        // Verify expense belongs to active mess
        if (!$activeMess || $expense->mess_id !== $activeMess->id) {
            abort(403, 'This expense does not belong to your current mess.');
        }
        
        // Get only approved members of the active mess
        $members = $activeMess->approvedMembers()->orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'activeMonth', 'members', 'activeMess'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(StoreExpenseRequest $request, Expense $expense)
    {
        $this->authorize('update', $expense);
        
        $activeMess = activeMess();
        
        // Verify expense belongs to active mess
        if (!$activeMess || $expense->mess_id !== $activeMess->id) {
            abort(403, 'This expense does not belong to your current mess.');
        }
        
        // Check if month is closed
        if (isMonthClosed($expense->month_id)) {
            return redirect()->back()
                ->with('error', 'This month is closed. No further modifications are allowed.');
        }
        
        $data = $request->validated();
        
        // Keep month_id as the active month
        $activeMonth = activeMonth();
        $data['month_id'] = $activeMonth->id;
        
        $expense->update($data);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense record updated successfully.');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);
        
        $activeMess = activeMess();
        
        // Verify expense belongs to active mess
        if (!$activeMess || $expense->mess_id !== $activeMess->id) {
            abort(403, 'This expense does not belong to your current mess.');
        }
        
        // Check if month is closed
        if (isMonthClosed($expense->month_id)) {
            return redirect()->back()
                ->with('error', 'This month is closed. No further deletions are allowed.');
        }
        
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense record deleted successfully.');
    }
}
