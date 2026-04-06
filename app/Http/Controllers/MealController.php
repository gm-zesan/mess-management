<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Models\Meal;
use App\Models\User;

class MealController extends Controller
{
    /**
     * Display a listing of the meals.
     */
    public function index()
    {
        $this->authorize('viewAny', Meal::class);
        
        $activeMess = activeMess();
        $activeMonth = activeMonth();
        
        if (!$activeMess || !$activeMonth) {
            return redirect(route('dashboard'))->with('error', 'No active mess or month found.');
        }
        
        // Get filter parameters
        $filterDate = request('filter_date');
        $filterMember = request('filter_member');
        
        // Base query filtered by mess_id
        $query = Meal::with(['user', 'month', 'mess'])
            ->where('mess_id', $activeMess->id)
            ->where('month_id', $activeMonth->id);
        
        // Apply date filter
        if ($filterDate) {
            $query->where('date', $filterDate);
        }
        
        // Apply member filter
        if ($filterMember) {
            $query->where('user_id', $filterMember);
        }
        
        $meals = $query->latest('date')->paginate(15);
        
        // Get only approved members of the active mess for filter dropdown
        $members = $activeMess->approvedMembers()->orderBy('name')->get();
        
        return view('meals.index', compact('meals', 'activeMess', 'activeMonth', 'members', 'filterDate', 'filterMember'));
    }

    /**
     * Show the form for creating a new meal.
     */
    public function create()
    {
        $this->authorize('create', Meal::class);
        
        $activeMess = activeMess();
        $activeMonth = activeMonth();
        
        if (!$activeMess || !$activeMonth) {
            return redirect(route('dashboard'))->with('error', 'No active mess or month found.');
        }
        
        // Get only approved members of the active mess
        $members = $activeMess->approvedMembers()->orderBy('name')->get();

        return view('meals.create', compact('members', 'activeMonth', 'activeMess'));
    }

    /**
     * Store a newly created meal in storage.
     */
    public function store(StoreMealRequest $request)
    {
        $this->authorize('create', Meal::class);
        // Get validated data
        $data = $request->validated();

        // Ensure mess and month exist
        $activeMess = activeMess();
        $activeMonth = activeMonth();
        
        if (!$activeMess || !$activeMonth) {
            return redirect()->back()
                ->with('error', 'No active mess or month found.');
        }
        
        // Check if month is closed
        if ($activeMonth->isClosed()) {
            return redirect()->back()
                ->with('error', 'This month is closed. No further modifications are allowed.');
        }

        // Prepare meals array for bulk creation
        $mealsToCreate = [];
        $date = $data['date'];

        // Create meal records for each member that has at least one meal
        foreach ($data['meals'] as $userId => $mealData) {
            // Skip if no meals selected for this user
            if (!isset($mealData['breakfast_count']) && !isset($mealData['lunch_count']) && !isset($mealData['dinner_count'])) {
                continue;
            }

            // Check if meal record already exists for this user on this date
            $existingMeal = Meal::where('user_id', $userId)
                ->where('mess_id', $activeMess->id)
                ->where('month_id', $activeMonth->id)
                ->where('date', $date)
                ->first();

            if ($existingMeal) {
                return redirect()->back()
                    ->with('error', "A meal record already exists for this user on {$date}. Please delete the existing record first.");
            }

            $mealsToCreate[] = [
                'mess_id' => $activeMess->id,
                'user_id' => $userId,
                'month_id' => $activeMonth->id,
                'date' => $date,
                'breakfast_count' => isset($mealData['breakfast_count']) ? floatval($mealData['breakfast_count']) : 0,
                'lunch_count' => isset($mealData['lunch_count']) ? floatval($mealData['lunch_count']) : 0,
                'dinner_count' => isset($mealData['dinner_count']) ? floatval($mealData['dinner_count']) : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (empty($mealsToCreate)) {
            return redirect()->back()
                ->with('error', 'Please select at least one meal for at least one member.');
        }

        // Bulk insert all meal records
        Meal::insert($mealsToCreate);

        return redirect()->route('meals.index')
            ->with('success', 'Meal records created successfully (' . count($mealsToCreate) . ' members).');
    }

    /**
     * Show the form for editing the specified meal.
     */
    public function edit(Meal $meal)
    {
        $this->authorize('update', $meal);
        
        $activeMess = activeMess();
        $activeMonth = activeMonth();
        
        // Verify meal belongs to active mess
        if (!$activeMess || $meal->mess_id !== $activeMess->id) {
            abort(403, 'This meal does not belong to your current mess.');
        }

        // Return JSON for modal (AJAX request)
        if (request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'id' => $meal->id,
                'user_id' => $meal->user_id,
                'user_name' => $meal->user->name,
                'date' => $meal->date->format('Y-m-d'),
                'date_display' => $meal->date->format('M d, Y'),
                'breakfast_count' => $meal->breakfast_count,
                'lunch_count' => $meal->lunch_count,
                'dinner_count' => $meal->dinner_count,
            ]);
        }

        // Return view for traditional page view
        $members = $activeMess->approvedMembers()->orderBy('name')->get();
        return view('meals.edit', compact('meal', 'members', 'activeMonth', 'activeMess'));
    }

    /**
     * Update the specified meal in storage.
     */
    public function update(UpdateMealRequest $request, Meal $meal)
    {
        $this->authorize('update', $meal);
        
        $activeMess = activeMess();
        
        // Verify meal belongs to active mess
        if (!$activeMess || $meal->mess_id !== $activeMess->id) {
            abort(403, 'This meal does not belong to your current mess.');
        }
        
        // Check if month is closed
        if ($meal->month->isClosed()) {
            return redirect()->back()
                ->with('error', 'This month is closed. No further modifications are allowed.');
        }
        
        // Get validated data
        $data = $request->validated();

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
        $this->authorize('delete', $meal);
        
        $activeMess = activeMess();
        
        // Verify meal belongs to active mess
        if (!$activeMess || $meal->mess_id !== $activeMess->id) {
            abort(403, 'This meal does not belong to your current mess.');
        }
        
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

