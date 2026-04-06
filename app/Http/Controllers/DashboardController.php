<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Services\CalculationService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function index(CalculationService $calculationService)
    {
        try {
            $activeMess = activeMess();
            $activeMonth = activeMonth();
            
            if (!$activeMess) {
                return redirect()->route('mess.selection')->with('error', 'Please select a mess first.');
            }
            
            if (!$activeMonth) {
                return view('dashboard', [
                    'activeMess' => $activeMess,
                    'activeMonth' => null,
                    'summary' => null,
                    'isClosed' => false,
                ]);
            }
            
            $summary = $calculationService->getMonthSummary($activeMonth, $activeMess->id);
            
            // Add user-specific data if not superadmin
            if (!isSuperAdminInMess()) {
                $user = Auth::user();
                
                // Get user meals for this month
                $userMealData = $activeMonth->meals()
                    ->where('user_id', $user->id)
                    ->where('mess_id', $activeMess->id)
                    ->selectRaw('SUM(breakfast_count + lunch_count + dinner_count) as total_meals')
                    ->first();
                
                $userMeals = (float) ($userMealData->total_meals ?? 0);
                $userMealCost = $userMeals * ($summary['meal_rate'] ?? 0);
                
                $userDeposits = Deposit::where('user_id', $user->id)
                    ->where('month_id', $activeMonth->id)
                    ->where('mess_id', $activeMess->id)
                    ->sum('amount');
                
                $userBalance = $userDeposits - $userMealCost;
                
                $summary['user_meals'] = $userMeals;
                $summary['user_meal_cost'] = $userMealCost;
                $summary['user_deposit'] = $userDeposits;
                $summary['user_balance'] = $userBalance;
            }
            
            return view('dashboard', [
                'activeMess' => $activeMess,
                'activeMonth' => $activeMonth,
                'summary' => $summary,
                'isClosed' => $activeMonth->isClosed(),
            ]);
        } catch (\Exception $e) {
            // No active month found
            $activeMess = activeMess();
            
            if (!$activeMess) {
                return redirect()->route('mess.selection')->with('error', 'Please select a mess first.');
            }
            
            return view('dashboard', [
                'activeMess' => $activeMess,
                'activeMonth' => null,
                'summary' => null,
                'isClosed' => false,
            ]);
        }
    }
}
