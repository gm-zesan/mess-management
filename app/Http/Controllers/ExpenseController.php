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
        $expenses = Expense::with('month', 'user')
            ->latest('date')
            ->paginate(15);

        return view('expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $activeMonth = activeMonth();
        $members = User::get();

        return view('expenses.create', compact('activeMonth', 'members'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(StoreExpenseRequest $request)
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
        Expense::create($data);

        // Check if also creating a deposit
        if ($request->has('with_deposit') && $request->input('with_deposit') == 1) {
            $user_id = $request->input('user_id');
            
            // Only create deposit if user_id is provided
            if ($user_id) {
                Deposit::create([
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
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        $activeMonth = activeMonth();
        $members = User::get();

        return view('expenses.edit', compact('expense', 'activeMonth', 'members'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(StoreExpenseRequest $request, Expense $expense)
    {
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
