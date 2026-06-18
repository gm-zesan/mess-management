<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\LoginAttemptService;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, LoginAttemptService $attemptService, AuditLogService $auditService): RedirectResponse
    {
        $email = $request->input('email');

        // Check if login is allowed (rate limiting & lockout)
        $loginCheck = $attemptService->isLoginAllowed($email);
        if (!$loginCheck['allowed']) {
            throw ValidationException::withMessages([
                'email' => $loginCheck['message'],
            ])->status(429); // Too Many Requests
        }

        // Attempt authentication
        if (!$request->authenticate()) {
            $attemptService->recordFailedLogin($email, 'Invalid credentials');
            $auditService->logLoginAttempt($email, false, 'Invalid credentials');
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Record successful login
        $attemptService->recordSuccessfulLogin($email);
        $auditService->logLoginAttempt($email, true);
        $request->session()->regenerate();

        // Check if user has an approved mess and redirect directly to it
        $approvedMess = Auth::user()->messUsers()
            ->where('status', 'approved')
            ->with('mess')
            ->first();

        if ($approvedMess) {
            $request->session()->put('mess_id', $approvedMess->mess_id);
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // If no approved mess, redirect to mess selection
        return redirect()->intended(route('mess.selection', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request, AuditLogService $auditService): RedirectResponse
    {
        // Log the logout before clearing auth
        $auditService->logLogout();

        Auth::guard('web')->logout();

        // Clear superadmin mess session
        $request->session()->forget('superadmin_mess_id');
        $request->session()->forget('mess_id');

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
