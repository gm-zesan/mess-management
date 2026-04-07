<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Models\Meal;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

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

            // AJAX request
            if (request()->ajax()) {
                // Join users so server-side search can filter by user name
                $query = Meal::select(
                        'meals.*',
                        'users.name as user_name'
                    )
                    ->leftJoin('users', 'users.id', '=', 'meals.user_id')
                    ->where('meals.mess_id', $activeMess->id)
                    ->where('meals.month_id', $activeMonth->id);

                // Apply filters
                if ($filterDate = request('filter_date')) {
                    $query->where('meals.date', $filterDate);
                }

                if ($filterMember = request('filter_member')) {
                    $query->where('meals.user_id', $filterMember);
                }

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('user', function($meal){
                        return $meal->user_name ?? '-';
                    })
                    ->addColumn('date', function($meal){
                        return Carbon::parse($meal->date)->format('d M Y');
                    })
                    ->addColumn('breakfast_count', fn($meal) => $meal->breakfast_count ?? 0)
                    ->addColumn('lunch_count', fn($meal) => $meal->lunch_count ?? 0)
                    ->addColumn('dinner_count', fn($meal) => $meal->dinner_count ?? 0)
                    ->addColumn('total_meal_count', fn($meal) => $meal->total_meal_count ?? 0)
                    ->addColumn('action', function($meal){
                        $editUrl = route('meals.edit', $meal->id);
                        $deleteUrl = route('meals.destroy', $meal->id);
                        $html = '<div class="flex items-center justify-center gap-2">';
                        if (request()->user()->can('update', $meal)) {
                            $html .= '<a href="'.$editUrl.'" data-id="'.$meal->id.'" class="edit-btn inline-flex items-center justify-center w-8 h-8 rounded hover:bg-sky-100 text-sky-600 hover:text-sky-700" title="Edit">'
                                .'<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">'
                                .'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>'
                                .'</svg>'
                            .'</a>';
                        }
                        if (request()->user()->can('delete', $meal)) {
                            // Render a delete button that JS will handle via AJAX
                            $html .= '<button data-id="'.$meal->id.'" class="delete-btn inline-flex items-center justify-center w-8 h-8 rounded hover:bg-red-100 text-red-600 hover:text-red-700" title="Delete">'
                                    .'<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">'
                                        .'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>'
                                    .'</svg>'
                                .'</button>';
                        }
                        $html .= '</div>';
                        return $html;
                    })
                    ->rawColumns(['action'])
                    // Permissions sent from backend so frontend can conditionally render action buttons
                    ->addColumn('can_edit', fn() => auth()->user()->can('meals.update'))
                    ->addColumn('can_delete', fn() => auth()->user()->can('meals.delete'))
                    ->make(true);
            }

        // Normal page load
        $members = $activeMess->approvedMembers()->orderBy('name')->get();

        return view('meals.index', compact('activeMess', 'activeMonth', 'members'));
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

        $message = 'Meal records created successfully (' . count($mealsToCreate) . ' members).';

        // Return JSON for AJAX, redirect for normal requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => $message, 'count' => count($mealsToCreate)], 201);
        }

        return redirect()->route('meals.index')
            ->with('success', $message);
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

        $message = 'Meal record updated successfully.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => $message, 'meal' => $meal], 200);
        }

        return redirect()->route('meals.index')
            ->with('success', $message);
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

        $message = 'Meal record deleted successfully.';

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['message' => $message], 200);
        }

        return redirect()->route('meals.index')
            ->with('success', $message);
    }
}

