<?php

use App\Http\Controllers\DepositController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\MessInviteController;
use App\Http\Controllers\MessSelectionController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Mess Selection (after registration)
    Route::get('/mess/selection', [MessSelectionController::class, 'show'])->name('mess.selection');
    Route::post('/mess/create', [MessSelectionController::class, 'create'])->name('mess.create');
    Route::post('/mess/{mess}/join', [MessSelectionController::class, 'join'])->name('mess.join');
    Route::get('/mess/pending-invitations', [MessSelectionController::class, 'pendingInvitations'])->name('mess.pending-invitations');
    Route::post('/mess/pending-invitations/{messUser}/approve', [MessSelectionController::class, 'approveUser'])->name('mess.approve-user');
    Route::post('/mess/pending-invitations/{messUser}/reject', [MessSelectionController::class, 'rejectUser'])->name('mess.reject-user');
    
    // Mess Invitations & Member Management
    Route::get('/mess/{mess}/profile', [MessInviteController::class, 'profile'])->name('mess.profile');
    Route::patch('/mess/{mess}/profile', [MessInviteController::class, 'updateProfile'])->name('mess.profile.update');
    Route::get('/mess/{mess}/invite', [MessInviteController::class, 'create'])->name('mess.invite');
    Route::post('/mess/{mess}/invite', [MessInviteController::class, 'store'])->name('mess.invite.store');
    
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
    Route::resource('months', MonthController::class)->except('show');
    Route::post('/months/{month}/close', [MonthController::class, 'close'])->name('months.close');
    
    Route::resource('meals', MealController::class)->except('show');    
    Route::resource('expenses', ExpenseController::class)->except('show');
    Route::resource('deposits', DepositController::class)->except('show');
    
    // Reports
    Route::get('/reports/all-months', [ReportController::class, 'allMonths'])->name('reports.all-months');
    Route::get('/reports/monthly/{month}', [ReportController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/reports/monthly/{month}/pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    
    // Permissions (Superadmin only)
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions/assign', [PermissionController::class, 'assignPermission'])->name('permissions.assign');
    Route::post('/permissions/revoke', [PermissionController::class, 'revokePermission'])->name('permissions.revoke');
});

require __DIR__.'/auth.php';
