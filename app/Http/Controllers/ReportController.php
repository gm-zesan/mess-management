<?php

namespace App\Http\Controllers;

use App\Models\Month;
use App\Models\User;
use App\Services\CalculationService;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Display monthly report.
     */
    public function monthlyReport(Month $month, CalculationService $calculationService)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Members can only view current month report
        if ($user->hasRole('member')) {
            if ($month->id !== activeMonth()->id) {
                abort(403, 'You can only view the current month report.');
            }
            // Skip policy authorization for members viewing current month
        } else {
            // Managers/Superadmins need proper authorization
            $this->authorize('view', $month);
        }
        
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
        /** @var User $user */
        $user = Auth::user();
        
        // Check if user has permission to view all months reports
        if (!$user->can('reports.all-months')) {
            abort(403, 'You do not have permission to access this page.');
        }
        
        // Members only see current month, managers/superadmins see all months
        if ($user->hasRole('member')) {
            $months = [activeMonth()];
        } else {
            $months = Month::all();
        }
        
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
        /** @var User $user */
        $user = Auth::user();
        
        // Members can only export current month report
        if ($user->hasRole('member')) {
            if ($month->id !== activeMonth()->id) {
                abort(403, 'You can only export the current month report.');
            }
            // Skip policy authorization for members exporting current month
        } else {
            // Managers/Superadmins need proper authorization
            $this->authorize('view', $month);
        }
        
        $summary = $calculationService->getMonthSummary($month);
        
        $html = view('reports.monthly-pdf', [
            'month' => $month,
            'summary' => $summary,
        ])->render();
        
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Monthly-Report-' . $month->name . '.pdf');
    }
}
