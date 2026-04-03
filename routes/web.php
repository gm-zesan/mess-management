<?php

use App\Http\Controllers\DepositController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\MemberAuthController;
use App\Http\Controllers\Auth\TestAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
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
    
    // Report routes
    Route::get('/reports/all-months', [ReportController::class, 'allMonths'])->name('reports.all-months');
    Route::get('/reports/monthly/{month}', [ReportController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/reports/monthly/{month}/pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
});

// Member Authentication Routes (View-Only)
Route::get('/member/login', [MemberAuthController::class, 'showLogin'])->name('member.login')->middleware('guest:member');
Route::post('/member/login', [MemberAuthController::class, 'login'])->name('member.login.submit')->middleware('guest:member');

Route::middleware('auth:member')->group(function () {
    Route::get('/member/dashboard', [MemberAuthController::class, 'dashboard'])->name('member.dashboard');
    Route::post('/member/logout', [MemberAuthController::class, 'logout'])->name('member.logout');
});

// Test authentication endpoints (for debugging)
Route::get('/test/auth', [TestAuthController::class, 'test'])->name('test.auth');
Route::get('/test/members', [TestAuthController::class, 'listMembers'])->name('test.members');

require __DIR__.'/auth.php';
