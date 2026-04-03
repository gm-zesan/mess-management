<?php

namespace App\Http\Controllers;

use App\Models\Month;
use App\Services\CalculationService;
use PDF;

class ReportController extends Controller
{
    /**
     * Display monthly report.
     */
    public function monthlyReport(Month $month, CalculationService $calculationService)
    {
        $this->authorize('view', $month);
        
        $summary = $calculationService->getMonthSummary($month);
        
        return view('reports.monthly', [
            'month' => $month,
            'summary' => $summary,
        ]);
    }

    /**
     * Display all months reports.
     */
    public function allMonths(CalculationService $calculationService)
    {
        // Members can view reports
        $this->authorize('viewAny', Month::class);
        
        $months = Month::all();
        
        $reports = [];
        foreach ($months as $month) {
            $reports[$month->id] = $calculationService->getMonthSummary($month);
        }
        
        return view('reports.all-months', compact('months', 'reports'));
    }

    /**
     * Export monthly report as PDF.
     */
    public function exportPdf(Month $month, CalculationService $calculationService)
    {
        $this->authorize('view', $month);
        
        $summary = $calculationService->getMonthSummary($month);
        
        $html = view('reports.monthly-pdf', [
            'month' => $month,
            'summary' => $summary,
        ])->render();
        
        $pdf = PDF::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Monthly-Report-' . $month->name . '.pdf');
    }
}
