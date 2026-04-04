<?php

namespace App\Http\Controllers;

use App\Services\CalculationService;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function index(CalculationService $calculationService)
    {
        try {
            $activeMess = activeMess();
            $activeMonth = $activeMess ? $activeMess->activeMonth : null;
            
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
