<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Expense::class);
        
        $activeMess = activeMess();
        $activeMonth = activeMonth();
        
        if (!$activeMess || !$activeMonth) {
            return redirect(route('dashboard'))->with('error', 'No active mess or month found.');
        }

        // AJAX request
        if ($request->ajax()) {
            // Join users so server-side search can filter by user name
            $query = Expense::select(
                    'expenses.id',
                    'expenses.date',
                    'expenses.category',
                    'expenses.amount',
                    'expenses.note',
                    'expenses.user_id',
                    'users.name as user_name'
                )
                ->leftJoin('users', 'users.id', '=', 'expenses.user_id')
                ->with('user')
                ->where('expenses.mess_id', $activeMess->id)
                ->where('expenses.month_id', $activeMonth->id);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('date', fn($expense) => $expense->date->format('d M Y'))
                ->addColumn('user', fn($expense) => $expense->user->name ?? '-')
                ->addColumn('category', fn($expense) => $expense->category ?? 'N/A')
                ->addColumn('amount', fn($expense) => '$' . number_format($expense->amount, 2))
                ->addColumn('description', fn($expense) => $expense->note ?? '-')
                // Permissions sent from backend so frontend can conditionally render action buttons
                ->addColumn('can_edit', fn() => auth()->user()->can('expenses.update'))
                ->addColumn('can_delete', fn() => auth()->user()->can('expenses.delete'))
                ->make(true);
        }

        // Normal page load
        $members = $activeMess->approvedMembers()->orderBy('name')->get();

        return view('expenses.index', compact('activeMess', 'activeMonth', 'members'));
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
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'No active mess or month found.'], 400);
            }
            return redirect()->back()
                ->with('error', 'No active mess or month found.');
        }
        
        // Check if month is closed
        if (isMonthClosed($activeMonth)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'This month is closed. No further modifications are allowed.'], 422);
            }
            return redirect()->back()
                ->with('error', 'This month is closed. No further modifications are allowed.');
        }
        
        $data['mess_id'] = $activeMess->id;
        $data['month_id'] = $activeMonth->id;
        $expense = Expense::create($data);

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

        // Return JSON for AJAX, redirect for normal requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => $message, 'expense' => $expense], 201);
        }

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
        
        // Return JSON for AJAX requests
        if (request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'id' => $expense->id,
                'user_id' => $expense->user_id,
                'user_name' => $expense->user->name,
                'date' => $expense->date->format('Y-m-d'),
                'formatted_date' => $expense->date->format('d M Y'),
                'category' => $expense->category,
                'amount' => $expense->amount,
                'note' => $expense->note,
            ]);
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
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'This expense does not belong to your current mess.'], 403);
            }
            abort(403, 'This expense does not belong to your current mess.');
        }
        
        // Check if month is closed
        if (isMonthClosed($expense->month_id)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'This month is closed. No further modifications are allowed.'], 422);
            }
            return redirect()->back()
                ->with('error', 'This month is closed. No further modifications are allowed.');
        }
        
        $data = $request->validated();
        
        // Keep month_id as the active month
        $activeMonth = activeMonth();
        $data['month_id'] = $activeMonth->id;
        
        $expense->update($data);

        $message = 'Expense record updated successfully.';

        // Return JSON for AJAX, redirect for normal requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => $message, 'expense' => $expense], 200);
        }

        return redirect()->route('expenses.index')
            ->with('success', $message);
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
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'This expense does not belong to your current mess.'], 403);
            }
            abort(403, 'This expense does not belong to your current mess.');
        }
        
        // Check if month is closed
        if (isMonthClosed($expense->month_id)) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'This month is closed. No further deletions are allowed.'], 422);
            }
            return redirect()->back()
                ->with('error', 'This month is closed. No further deletions are allowed.');
        }
        
        $expense->delete();

        $message = 'Expense record deleted successfully.';

        // Return JSON for AJAX, redirect for normal requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['message' => $message], 200);
        }

        return redirect()->route('expenses.index')
            ->with('success', $message);
    }
}
