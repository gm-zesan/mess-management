<?php

namespace App\Services;

use App\Models\Month;
use App\Models\User;
use App\Models\Meal;
use App\Models\Expense;
use App\Models\Deposit;

class CalculationService
{
    /**
     * Calculate total meals for a given month.
     * Sums breakfast_count + lunch_count + dinner_count for all meals.
     *
     * @param int|Month $month
     * @return float
     */
    public function getTotalMeals($month): float
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        
        $result = Meal::where('month_id', $monthId)
            ->selectRaw('SUM(breakfast_count + lunch_count + dinner_count) as total_meals')
            ->first();
        
        return (float) ($result->total_meals ?? 0);
    }

    /**
     * Calculate total expenses for a given month.
     *
     * @param int|Month $month
     * @return float
     */
    public function getTotalExpenses($month): float
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        return (float) Expense::where('month_id', $monthId)->sum('amount');
    }

    /**
     * Calculate meal rate (cost per meal) for a given month.
     * Returns 0 if no meals exist.
     *
     * @param int|Month $month
     * @return float
     */
    public function getMealRate($month): float
    {
        $totalMeals = $this->getTotalMeals($month);
        
        if ($totalMeals == 0) {
            return 0;
        }

        $totalExpenses = $this->getTotalExpenses($month);
        return round($totalExpenses / $totalMeals, 2);
    }

    /**
     * Calculate total deposits for a given month.
     *
     * @param int|Month $month
     * @return float
     */
    public function getTotalDeposits($month): float
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        return (float) Deposit::where('month_id', $monthId)->sum('amount');
    }

    /**
     * Get per-user balance details for a given month.
     * Filters only users with 'manager' or 'member' role.
     *
     * @param int|Month $month
     * @return array
     */
    public function getPerMemberBalance($month): array
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        
        // Get total calculations
        $totalMeals = $this->getTotalMeals($month);
        $mealRate = $this->getMealRate($month);
        
        // Get all users with 'manager' or 'member' role who have meals or deposits
        $users = User::role(['manager', 'member'])
            ->where(function($q) use ($monthId) {
                $q->whereHas('meals', function($subQuery) use ($monthId) {
                    $subQuery->where('month_id', $monthId);
                })->orWhereHas('deposits', function($subQuery) use ($monthId) {
                    $subQuery->where('month_id', $monthId);
                });
            })
            ->get();
        
        $balances = [];
        
        foreach ($users as $user) {
            // Calculate user's total meals (breakfast + lunch + dinner)
            $userMealResult = Meal::where('month_id', $monthId)
                ->where('user_id', $user->id)
                ->selectRaw('SUM(breakfast_count + lunch_count + dinner_count) as total_meals')
                ->first();
            
            $userMeals = (float) ($userMealResult->total_meals ?? 0);
            
            // Calculate user's meal cost
            $mealCost = $userMeals * $mealRate;
            
            // Calculate user's total deposit
            $userDeposit = (float) Deposit::where('month_id', $monthId)
                ->where('user_id', $user->id)
                ->sum('amount');
            
            // Calculate balance (deposit - cost)
            $balance = $userDeposit - $mealCost;
            
            // Use user name as key for template compatibility
            $balances[$user->name] = [
                'user_id' => $user->id,
                'member_name' => $user->name,
                'meals' => $userMeals,
                'meal_rate' => $mealRate,
                'meal_cost' => round($mealCost, 2),
                'deposited' => round($userDeposit, 2),
                'balance' => round($balance, 2),
                'status' => $balance > 0 ? 'credit' : ($balance < 0 ? 'due' : 'settled'),
            ];
        }
        
        return $balances;
    }

    /**
     * Get user-specific summary for a given month.
     *
     * @param User $user
     * @param int|Month $month
     * @return array
     */
    public function getMemberMonthSummary(User $user, $month): array
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        
        // Calculate meal rate for this month
        $mealRate = $this->getMealRate($month);
        
        // Calculate user's total meals (breakfast + lunch + dinner)
        $mealResult = Meal::where('month_id', $monthId)
            ->where('user_id', $user->id)
            ->selectRaw('SUM(breakfast_count + lunch_count + dinner_count) as total_meals')
            ->first();
        
        $mealCount = (float) ($mealResult->total_meals ?? 0);
        
        // Calculate user's meal cost
        $mealCost = $mealCount * $mealRate;
        
        // Calculate user's total deposits
        $deposits = (float) Deposit::where('month_id', $monthId)
            ->where('user_id', $user->id)
            ->sum('amount');
        
        // Calculate balance (deposit - cost)
        $balance = $deposits - $mealCost;
        
        return [
            'user_id' => $user->id,
            'meal_count' => $mealCount,
            'meal_rate' => $mealRate,
            'meal_cost' => round($mealCost, 2),
            'deposits' => round($deposits, 2),
            'balance' => round($balance, 2),
            'status' => $balance > 0 ? 'credit' : ($balance < 0 ? 'due' : 'settled'),
        ];
    }

    /**
     * Get complete summary for a given month.
     *
     * @param int|Month $month
     * @return array
     */
    public function getMonthSummary($month): array
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        $monthObject = $month instanceof Month ? $month : Month::find($monthId);
        
        $totalMeals = $this->getTotalMeals($month);
        $totalExpenses = $this->getTotalExpenses($month);
        $totalDeposits = $this->getTotalDeposits($month);
        $mealRate = $this->getMealRate($month);
        $memberBalances = $this->getPerMemberBalance($month);
        
        return [
            'month_id' => $monthId,
            'month_name' => $monthObject->name ?? 'N/A',
            'total_meals' => $totalMeals,
            'total_expenses' => round($totalExpenses, 2),
            'total_deposits' => round($totalDeposits, 2),
            'meal_rate' => $mealRate,
            'member_balances' => $memberBalances,
            'total_members' => count($memberBalances),
            'net_balance' => round($totalDeposits - $totalExpenses, 2),
        ];
    }
}
