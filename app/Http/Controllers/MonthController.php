<?php

namespace App\Http\Controllers;

use App\Models\Month;
use App\Models\User;
use App\Models\Meal;
use App\Models\Deposit;
use App\Enums\MonthStatusEnum;
use App\Enums\RoleEnum;
use App\Services\MonthService;
use App\Services\CalculationService;
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
     * Display the specified resource with summary.
     */
    public function show(Month $month, CalculationService $calculationService)
    {
        $this->authorize('view', $month);
        
        $summary = $calculationService->getMonthSummary($month);

        return view('months.show', [
            'month' => $month,
            'totalMeals' => $summary['total_meals'],
            'totalExpenses' => $summary['total_expenses'],
            'totalDeposits' => $summary['total_deposits'],
            'costPerMeal' => $summary['meal_rate'],
            'memberBalance' => $summary['member_balances'],
        ]);
    }

    /**
     * Display detailed monthly report per member.
     */
    public function report(Month $month, CalculationService $calculationService)
    {
        $this->authorize('view', $month);
        
        $summary = $calculationService->getMonthSummary($month);
        
        // Get detailed meal and deposit records for each member
        $memberDetails = [];
        $members = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', RoleEnum::SUPERADMIN->value);
        })->get();
        $mealRate = $summary['meal_rate'];

        foreach ($members as $member) {
            $meals = Meal::where('month_id', $month->id)
                ->where('user_id', $member->id)
                ->orderBy('date')
                ->get();

            $deposits = Deposit::where('month_id', $month->id)
                ->where('user_id', $member->id)
                ->orderBy('date')
                ->get();

            $totalMealCount = $meals->sum('meal_count');
            $totalDeposited = $deposits->sum('amount');
            $mealCost = $totalMealCount * $mealRate;
            $balance = $totalDeposited - $mealCost;

            $memberDetails[$member->id] = [
                'name' => $member->name,
                'meals' => $meals,
                'total_meal_count' => $totalMealCount,
                'deposits' => $deposits,
                'total_deposited' => $totalDeposited,
                'meal_cost' => round($mealCost, 2),
                'balance' => round($balance, 2),
            ];
        }

        return view('months.report', [
            'month' => $month,
            'totalMeals' => $summary['total_meals'],
            'totalExpenses' => $summary['total_expenses'],
            'totalDeposits' => $summary['total_deposits'],
            'costPerMeal' => $summary['meal_rate'],
            'memberBalance' => $summary['member_balances'],
            'memberDetails' => $memberDetails,
        ]);
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
