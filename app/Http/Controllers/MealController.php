<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMealRequest;
use App\Models\Meal;
use App\Models\User;

class MealController extends Controller
{
    /**
     * Display a listing of the meals.
     */
    public function index()
    {
        $meals = Meal::with(['user', 'month'])
            ->latest('date')
            ->paginate(15);

        return view('meals.index', compact('meals'));
    }

    /**
     * Show the form for creating a new meal.
     */
    public function create()
    {
        $members = User::get();
        $activeMonth = activeMonth();

        return view('meals.create', compact('members', 'activeMonth'));
    }

    /**
     * Store a newly created meal in storage.
     */
    public function store(StoreMealRequest $request)
    {
        // Get validated data
        $data = $request->validated();

        // Ensure month_id is the active month
        $activeMonth = activeMonth();
        
        // Check if month is closed
        if ($activeMonth->isClosed()) {
            return redirect()->back()
                ->with('error', 'This month is closed. No further modifications are allowed.');
        }

        $data['month_id'] = $activeMonth->id;

        // Create the meal record
        Meal::create($data);

        return redirect()->route('meals.index')
            ->with('success', 'Meal record created successfully.');
    }

    /**
     * Display the specified meal.
     */
    public function show(Meal $meal)
    {
        return view('meals.show', compact('meal'));
    }

    /**
     * Show the form for editing the specified meal.
     */
    public function edit(Meal $meal)
    {
        $members = User::get();
        $activeMonth = activeMonth();

        return view('meals.edit', compact('meal', 'members', 'activeMonth'));
    }

    /**
     * Update the specified meal in storage.
     */
    public function update(StoreMealRequest $request, Meal $meal)
    {
        // Check if month is closed
        if ($meal->month->isClosed()) {
            return redirect()->back()
                ->with('error', 'This month is closed. No further modifications are allowed.');
        }
        
        // Get validated data
        $data = $request->validated();

        // Ensure month_id is the active month
        $activeMonth = activeMonth();
        $data['month_id'] = $activeMonth->id;

        // Update the meal record
        $meal->update($data);

        return redirect()->route('meals.index')
            ->with('success', 'Meal record updated successfully.');
    }

    /**
     * Remove the specified meal from storage.
     */
    public function destroy(Meal $meal)
    {
        // Check if month is closed
        if (isMonthClosed($meal->month_id)) {
            return redirect()->back()
                ->with('error', 'This month is closed. No further deletions are allowed.');
        }
        
        $meal->delete();

        return redirect()->route('meals.index')
            ->with('success', 'Meal record deleted successfully.');
    }
}

