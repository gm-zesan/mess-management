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
     * Calculate total meals for a given month and mess.
     * Sums breakfast_count + lunch_count + dinner_count for all meals.
     *
     * @param int|Month $month
     * @param int $messId
     * @return float
     */
    public function getTotalMeals($month, $messId = null): float
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        
        $query = Meal::where('month_id', $monthId);
        
        if ($messId) {
            $query->where('mess_id', $messId);
        }
        
        $result = $query->selectRaw('SUM(breakfast_count + lunch_count + dinner_count) as total_meals')
            ->first();
        
        return (float) ($result->total_meals ?? 0);
    }

    /**
     * Calculate total expenses for a given month and mess.
     *
     * @param int|Month $month
     * @param int $messId
     * @return float
     */
    public function getTotalExpenses($month, $messId = null): float
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        
        $query = Expense::where('month_id', $monthId);
        
        if ($messId) {
            $query->where('mess_id', $messId);
        }
        
        return (float) $query->sum('amount');
    }

    /**
     * Calculate meal rate (cost per meal) for a given month and mess.
     * Returns 0 if no meals exist.
     *
     * @param int|Month $month
     * @param int $messId
     * @return float
     */
    public function getMealRate($month, $messId = null): float
    {
        $totalMeals = $this->getTotalMeals($month, $messId);
        
        if ($totalMeals == 0) {
            return 0;
        }

        $totalExpenses = $this->getTotalExpenses($month, $messId);
        return round($totalExpenses / $totalMeals, 2);
    }

    /**
     * Calculate total deposits for a given month and mess.
     *
     * @param int|Month $month
     * @param int $messId
     * @return float
     */
    public function getTotalDeposits($month, $messId = null): float
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        
        $query = Deposit::where('month_id', $monthId);
        
        if ($messId) {
            $query->where('mess_id', $messId);
        }
        
        return (float) $query->sum('amount');
    }

    /**
     * Get per-user balance details for a given month and mess.
     * Filters only users with 'manager' or 'member' role.
     *
     * @param int|Month $month
     * @param int $messId
     * @return array
     */
    public function getPerMemberBalance($month, $messId = null): array
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        
        // Get total calculations with mess filter
        $totalMeals = $this->getTotalMeals($month, $messId);
        $mealRate = $this->getMealRate($month, $messId);
        
        // Get all users with 'manager' or 'member' role who have meals or deposits
        $query = User::role(['manager', 'member'])
            ->where(function($q) use ($monthId, $messId) {
                $q->whereHas('meals', function($subQuery) use ($monthId, $messId) {
                    $subQuery->where('month_id', $monthId);
                    if ($messId) {
                        $subQuery->where('mess_id', $messId);
                    }
                })->orWhereHas('deposits', function($subQuery) use ($monthId, $messId) {
                    $subQuery->where('month_id', $monthId);
                    if ($messId) {
                        $subQuery->where('mess_id', $messId);
                    }
                });
            });
        
        $users = $query->get();
        
        $balances = [];
        
        foreach ($users as $user) {
            // Calculate user's total meals (breakfast + lunch + dinner)
            $mealQuery = Meal::where('month_id', $monthId)
                ->where('user_id', $user->id);
            
            if ($messId) {
                $mealQuery->where('mess_id', $messId);
            }
            
            $userMealResult = $mealQuery
                ->selectRaw('SUM(breakfast_count + lunch_count + dinner_count) as total_meals')
                ->first();
            
            $userMeals = (float) ($userMealResult->total_meals ?? 0);
            
            // Calculate user's meal cost
            $mealCost = $userMeals * $mealRate;
            
            // Calculate user's total deposit
            $depositQuery = Deposit::where('month_id', $monthId)
                ->where('user_id', $user->id);
            
            if ($messId) {
                $depositQuery->where('mess_id', $messId);
            }
            
            $userDeposit = (float) $depositQuery->sum('amount');
            
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
     * Get user-specific summary for a given month and mess.
     *
     * @param User $user
     * @param int|Month $month
     * @param int $messId
     * @return array
     */
    public function getMemberMonthSummary(User $user, $month, $messId = null): array
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        
        // Calculate meal rate for this month and mess
        $mealRate = $this->getMealRate($month, $messId);
        
        // Calculate user's total meals (breakfast + lunch + dinner)
        $mealQuery = Meal::where('month_id', $monthId)
            ->where('user_id', $user->id);
        
        if ($messId) {
            $mealQuery->where('mess_id', $messId);
        }
        
        $mealResult = $mealQuery
            ->selectRaw('SUM(breakfast_count + lunch_count + dinner_count) as total_meals')
            ->first();
        
        $mealCount = (float) ($mealResult->total_meals ?? 0);
        
        // Calculate user's meal cost
        $mealCost = $mealCount * $mealRate;
        
        // Calculate user's total deposits
        $depositQuery = Deposit::where('month_id', $monthId)
            ->where('user_id', $user->id);
        
        if ($messId) {
            $depositQuery->where('mess_id', $messId);
        }
        
        $deposits = (float) $depositQuery->sum('amount');
        
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
     * Get complete summary for a given month and mess.
     *
     * @param int|Month $month
     * @param int $messId
     * @return array
     */
    public function getMonthSummary($month, $messId = null): array
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        $monthObject = $month instanceof Month ? $month : Month::find($monthId);
        
        $totalMeals = $this->getTotalMeals($month, $messId);
        $totalExpenses = $this->getTotalExpenses($month, $messId);
        $totalDeposits = $this->getTotalDeposits($month, $messId);
        $mealRate = $this->getMealRate($month, $messId);
        $memberBalances = $this->getPerMemberBalance($month, $messId);
        
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
