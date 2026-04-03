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
            $activeMonth = activeMonth();
            
            if (!$activeMonth) {
                return view('dashboard', [
                    'activeMonth' => null,
                    'summary' => null,
                    'isClosed' => false,
                    'error' => 'No active month found. Please create and activate a month first.',
                ]);
            }
            
            $summary = $calculationService->getMonthSummary($activeMonth);
            
            return view('dashboard', [
                'activeMonth' => $activeMonth,
                'summary' => $summary,
                'isClosed' => $activeMonth->isClosed(),
            ]);
        } catch (\Exception $e) {
            // No active month found
            return view('dashboard', [
                'activeMonth' => null,
                'summary' => null,
                'isClosed' => false,
                'error' => 'No active month found. Please create and activate a month first.',
            ]);
        }
    }
}
