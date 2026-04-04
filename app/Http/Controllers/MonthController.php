<?php

namespace App\Http\Controllers;

use App\Models\Month;
use App\Enums\MonthStatusEnum;
use App\Services\MonthService;
use Illuminate\Http\Request;

class MonthController extends Controller
{
    /**
     * Display a listing of months for the current mess.
     */
    public function index()
    {
        $this->authorize('viewAny', Month::class);
        
        $activeMess = activeMess();
        
        if (!$activeMess) {
            return redirect()->route('mess.selection')->with('error', 'Please select a mess first.');
        }
        
        // Get only months belonging to the active mess
        $months = $activeMess->months()->orderBy('start_date', 'desc')->paginate(15);
        
        return view('months.index', compact('months', 'activeMess'));
    }

    /**
     * Show the form for creating a new month for the active mess.
     */
    public function create()
    {
        $this->authorize('create', Month::class);
        
        $activeMess = activeMess();
        
        if (!$activeMess) {
            return redirect()->route('mess.selection')->with('error', 'Please select a mess first.');
        }
        
        return view('months.create', compact('activeMess'));
    }

    /**
     * Store a newly created month for the active mess.
     */
    public function store(Request $request, MonthService $monthService)
    {
        $this->authorize('create', Month::class);
        
        $activeMess = activeMess();
        
        if (!$activeMess) {
            return redirect()->route('mess.selection')->with('error', 'Please select a mess first.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:months',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:' . MonthStatusEnum::ACTIVE->value . ',' . MonthStatusEnum::CLOSED->value,
        ]);

        // Automatically assign the active mess to the month
        $validated['mess_id'] = $activeMess->id;
        $month = Month::create($validated);

        // If status is active, ensure only this month is active for this mess
        if ($month->status === MonthStatusEnum::ACTIVE) {
            $monthService->activateMonth($month);
        }

        return redirect()->route('months.index')->with('success', 'Month created successfully for ' . $activeMess->name . '.');
    }

    /**
     * Show the form for editing the specified month.
     */
    public function edit(Month $month)
    {
        $this->authorize('update', $month);
        
        $activeMess = activeMess();
        
        // Verify month belongs to active mess
        if (!$activeMess || $month->mess_id !== $activeMess->id) {
            abort(403, 'This month does not belong to your current mess.');
        }
        
        return view('months.edit', compact('month', 'activeMess'));
    }

    /**
     * Update the specified month.
     */
    public function update(Request $request, Month $month, MonthService $monthService)
    {
        $this->authorize('update', $month);
        
        $activeMess = activeMess();
        
        // Verify month belongs to active mess
        if (!$activeMess || $month->mess_id !== $activeMess->id) {
            abort(403, 'This month does not belong to your current mess.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:months,name,' . $month->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:' . MonthStatusEnum::ACTIVE->value . ',' . MonthStatusEnum::CLOSED->value,
        ]);

        $month->update($validated);

        // If status is being set to active, ensure only this month is active for this mess
        if ($month->status === MonthStatusEnum::ACTIVE) {
            $monthService->activateMonth($month);
        }

        return redirect()->route('months.index')->with('success', 'Month updated successfully.');
    }

    /**
     * Close a month (prevent further modifications).
     */
    public function close(Month $month, MonthService $monthService)
    {
        $this->authorize('update', $month);
        
        $activeMess = activeMess();
        
        // Verify month belongs to active mess
        if (!$activeMess || $month->mess_id !== $activeMess->id) {
            abort(403, 'This month does not belong to your current mess.');
        }
        
        $monthService->closeMonth($month);
        
        return redirect()->route('months.index')
            ->with('success', 'Month has been closed. No further modifications are allowed.');
    }

    /**
     * Remove the specified month.
     */
    public function destroy(Month $month)
    {
        $this->authorize('delete', $month);
        
        $activeMess = activeMess();
        
        // Verify month belongs to active mess
        if (!$activeMess || $month->mess_id !== $activeMess->id) {
            abort(403, 'This month does not belong to your current mess.');
        }
        
        $month->delete();
        return redirect()->route('months.index')->with('success', 'Month deleted successfully.');
    }
}
