<?php

use App\Http\Controllers\DepositController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Resource routes with automatic policy authorization
    Route::resource('members', MemberController::class)->except('show');
    Route::post('/members/{member}/change-manager', [MemberController::class, 'changeManager'])
        ->name('members.change-manager');
    Route::resource('months', MonthController::class);
    Route::post('/months/{month}/close', [MonthController::class, 'close'])->name('months.close');
    Route::get('/months/{month}/report', [MonthController::class, 'report'])->name('months.report');
    
    Route::resource('meals', MealController::class)->except('show');    
    Route::resource('expenses', ExpenseController::class)->except('show');
    Route::resource('deposits', DepositController::class)->except('show');
    
    // Reports
    Route::get('/reports/all-months', [ReportController::class, 'allMonths'])->name('reports.all-months');
    Route::get('/reports/monthly/{month}', [ReportController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/reports/monthly/{month}/pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
});

require __DIR__.'/auth.php';
