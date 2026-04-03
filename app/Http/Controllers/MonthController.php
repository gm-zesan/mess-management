<?php

namespace App\Http\Controllers;

use App\Models\Month;
use App\Enums\MonthStatusEnum;
use App\Services\MonthService;
use Illuminate\Http\Request;

class MonthController extends Controller
{
    /**
     * Display a listing of all months with summaries.
     */
    public function index()
    {
        $this->authorize('viewAny', Month::class);
        
        $months = Month::all();
        return view('months.index', compact('months'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Month::class);
        
        return view('months.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, MonthService $monthService)
    {
        $this->authorize('create', Month::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:months',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:' . MonthStatusEnum::ACTIVE->value . ',' . MonthStatusEnum::CLOSED->value,
        ]);

        $month = Month::create($validated);

        // If status is active, ensure only this month is active
        if ($month->status === MonthStatusEnum::ACTIVE) {
            $monthService->activateMonth($month);
        }

        return redirect()->route('months.index')->with('success', 'Month created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Month $month)
    {
        $this->authorize('update', $month);
        
        return view('months.edit', compact('month'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Month $month, MonthService $monthService)
    {
        $this->authorize('update', $month);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:months,name,' . $month->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:' . MonthStatusEnum::ACTIVE->value . ',' . MonthStatusEnum::CLOSED->value,
        ]);

        $month->update($validated);

        // If status is being set to active, ensure only this month is active
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
        
        $monthService->closeMonth($month);
        
        return redirect()->route('months.show', $month)
            ->with('success', 'Month has been closed. No further modifications are allowed.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Month $month)
    {
        $this->authorize('delete', $month);
        
        $month->delete();
        return redirect()->route('months.index')->with('success', 'Month deleted successfully.');
    }
}
