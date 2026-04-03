<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     */
    public function index()
    {
        $expenses = Expense::with('month')
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

        return view('expenses.create', compact('activeMonth'));
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

        return redirect()->route('expenses.index')
            ->with('success', 'Expense record created successfully.');
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

        return view('expenses.edit', compact('expense', 'activeMonth'));
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
