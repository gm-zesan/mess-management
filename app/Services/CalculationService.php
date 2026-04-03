<?php

namespace App\Services;

use App\Models\Month;
use App\Models\Member;
use App\Models\Meal;
use App\Models\Expense;
use App\Models\Deposit;

class CalculationService
{
    /**
     * Calculate total meals for a given month.
     *
     * @param int|Month $month
     * @return int
     */
    public function getTotalMeals($month): int
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        return Meal::where('month_id', $monthId)->sum('meal_count');
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
     * Get per-member balance details for a given month.
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
        
        // Get all active members
        $members = Member::where('status', 'active')->get();
        
        $balances = [];
        
        foreach ($members as $member) {
            // Calculate member's total meals
            $memberMeals = Meal::where('month_id', $monthId)
                ->where('member_id', $member->id)
                ->sum('meal_count');
            
            // Calculate member's meal cost
            $mealCost = $memberMeals * $mealRate;
            
            // Calculate member's total deposit
            $memberDeposit = (float) Deposit::where('month_id', $monthId)
                ->where('member_id', $member->id)
                ->sum('amount');
            
            // Calculate balance (deposit - cost)
            $balance = $memberDeposit - $mealCost;
            
            // Use member name as key for template compatibility
            $balances[$member->name] = [
                'member_id' => $member->id,
                'member_name' => $member->name,
                'meals' => $memberMeals,
                'meal_rate' => $mealRate,
                'meal_cost' => round($mealCost, 2),
                'deposited' => round($memberDeposit, 2),
                'balance' => round($balance, 2),
                'status' => $balance > 0 ? 'credit' : ($balance < 0 ? 'due' : 'settled'),
            ];
        }
        
        return $balances;
    }

    /**
     * Get member-specific summary for a given month.
     *
     * @param Member $member
     * @param int|Month $month
     * @return array
     */
    public function getMemberMonthSummary(Member $member, $month): array
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        
        // Calculate meal rate for this month
        $mealRate = $this->getMealRate($month);
        
        // Calculate member's total meals
        $mealCount = (int) Meal::where('month_id', $monthId)
            ->where('member_id', $member->id)
            ->sum('meal_count');
        
        // Calculate member's meal cost
        $mealCost = $mealCount * $mealRate;
        
        // Calculate member's total deposits
        $deposits = (float) Deposit::where('month_id', $monthId)
            ->where('member_id', $member->id)
            ->sum('amount');
        
        // Calculate balance (deposit - cost)
        $balance = $deposits - $mealCost;
        
        return [
            'member_id' => $member->id,
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
