<?php

namespace App\Services;

use App\Models\LoginAttempt;
use Illuminate\Support\Facades\Log;

class LoginAttemptService
{
    /**
     * Check if a login attempt is allowed.
     * @param string $email
     * @param string|null $ipAddress
     * @return array ['allowed' => bool, 'message' => string|null, 'lockout_minutes' => int]
     */
    public function isLoginAllowed(string $email, ?string $ipAddress = null): array
    {
        if (!config('auth.login_attempts.enabled')) {
            return ['allowed' => true, 'message' => null, 'lockout_minutes' => 0];
        }

        $ipAddress = $ipAddress ?? request()->ip();

        // Check if email is locked out
        if (LoginAttempt::isLockedOut($email)) {
            $remaining = LoginAttempt::getLockoutTimeRemaining($email);
            return [
                'allowed' => false,
                'message' => "Account temporarily locked. Try again in {$remaining} minutes.",
                'lockout_minutes' => $remaining,
            ];
        }

        // Check if IP is locked out
        if (LoginAttempt::isIpLockedOut($ipAddress)) {
            $remaining = $this->getIpLockoutTimeRemaining($ipAddress);
            return [
                'allowed' => false,
                'message' => "Too many login attempts from this IP. Try again in {$remaining} minutes.",
                'lockout_minutes' => $remaining,
            ];
        }

        return ['allowed' => true, 'message' => null, 'lockout_minutes' => 0];
    }

    /**
     * Record a successful login attempt.
     * @param string $email
     * @return void
     */
    public function recordSuccessfulLogin(string $email): void
    {
        LoginAttempt::record($email, true);

        Log::info('Successful login attempt', [
            'email' => $email,
            'ip' => request()->ip(),
        ]);
    }

    /**
     * Record a failed login attempt.
     * @param string $email
     * @param string $reason
     * @return void
     */
    public function recordFailedLogin(string $email, string $reason = 'Invalid credentials'): void
    {
        LoginAttempt::record($email, false, $reason);

        $failedCount = LoginAttempt::getRecentFailedAttempts($email);
        $maxAttempts = config('auth.login_attempts.max_attempts', 5);

        Log::warning('Failed login attempt', [
            'email' => $email,
            'ip' => request()->ip(),
            'reason' => $reason,
            'attempts' => $failedCount,
            'max_attempts' => $maxAttempts,
        ]);
    }

    /**
     * Get IP lockout time remaining in minutes.
     * @param string $ipAddress
     * @return int
     */
    private function getIpLockoutTimeRemaining(string $ipAddress): int
    {
        $lockoutMinutes = config('auth.login_attempts.lockout_minutes', 15);
        $lastFailedAttempt = LoginAttempt::where('ip_address', $ipAddress)
            ->where('successful', false)
            ->latest('created_at')
            ->first();

        if (!$lastFailedAttempt) {
            return 0;
        }

        $lockoutUntil = $lastFailedAttempt->created_at->addMinutes($lockoutMinutes);
        $remaining = $lockoutUntil->diffInMinutes(now(), false);

        return max(0, $remaining);
    }

    /**
     * Clear lockout for an email (admin function).
     * @param string $email
     * @return int
     */
    public function clearLockout(string $email): int
    {
        $lockoutMinutes = config('auth.login_attempts.lockout_minutes', 15);

        return LoginAttempt::where('email', $email)
            ->where('successful', false)
            ->where('created_at', '>=', now()->subMinutes($lockoutMinutes))
            ->delete();
    }

    /**
     * Clean up old login attempts (scheduled task).
     * @return int
     */
    public function cleanupOldAttempts(): int
    {
        $retentionDays = config('auth.login_attempts.retention_days', 90);
        return LoginAttempt::clearOldAttempts($retentionDays);
    }
}
