<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
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
        $activeMess = activeMess();
        
        if (!$activeMess) {
            abort(403, 'You must be a member of a mess to view reports.');
        }
        
        // Verify month belongs to active mess
        if ($month->mess_id !== $activeMess->id) {
            abort(403, 'This month does not belong to your mess.');
        }
        
        // Members can only view current month report
        if ($user->hasRole(RoleEnum::MEMBER->value)) {
            if ($month->id !== activeMonth()->id) {
                abort(403, 'You can only view the current month report.');
            }
            // Skip policy authorization for members viewing current month
        } else {
            // Managers/Superadmins need proper authorization
            $this->authorize('view', $month);
        }
        
        $summary = $calculationService->getMonthSummary($month, $activeMess->id);
        
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
        $activeMess = activeMess();
        
        if (!$activeMess) {
            abort(403, 'You must be a member of a mess to view reports.');
        }
        
        // Check if user has permission to view all months reports
        if (!$user->can('reports.all-months')) {
            abort(403, 'You do not have permission to access this page.');
        }
        
        // Get all months for the active mess (or current month for members)
        if ($user->hasRole(RoleEnum::MEMBER->value)) {
            $months = [activeMonth()];
        } else {
            // Managers and superadmins see all months for their mess
            $months = $activeMess->months()->get();
        }
        
        $reports = [];
        foreach ($months as $month) {
            $reports[$month->id] = $calculationService->getMonthSummary($month, $activeMess->id);
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
        $activeMess = activeMess();
        
        if (!$activeMess) {
            abort(403, 'You must be a member of a mess to export reports.');
        }
        
        // Verify month belongs to active mess
        if ($month->mess_id !== $activeMess->id) {
            abort(403, 'This month does not belong to your mess.');
        }
        
        // Members can only export current month report
        if ($user->hasRole(RoleEnum::MEMBER->value)) {
            if ($month->id !== activeMonth()->id) {
                abort(403, 'You can only export the current month report.');
            }
            // Skip policy authorization for members exporting current month
        } else {
            // Managers/Superadmins need proper authorization
            $this->authorize('view', $month);
        }
        
        $summary = $calculationService->getMonthSummary($month, $activeMess->id);
        
        $html = view('reports.monthly-pdf', [
            'month' => $month,
            'summary' => $summary,
        ])->render();
        
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Monthly-Report-' . $month->name . '.pdf');
    }
}
