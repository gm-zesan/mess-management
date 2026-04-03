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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('members', MemberController::class);
    Route::resource('months', MonthController::class);
    Route::get('/months/{month}/report', [MonthController::class, 'report'])->name('months.report');
    Route::post('/months/{month}/close', [MonthController::class, 'close'])->name('months.close');
    Route::resource('meals', MealController::class);    
    Route::resource('expenses', ExpenseController::class);
    Route::resource('deposits', DepositController::class);
    
    Route::get('/reports/all-months', [ReportController::class, 'allMonths'])->name('reports.all-months');
    Route::get('/reports/monthly/{month}', [ReportController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/reports/monthly/{month}/pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
});

require __DIR__.'/auth.php';
