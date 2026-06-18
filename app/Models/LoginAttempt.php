<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'successful',
        'failure_reason',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get recent failed login attempts for an email in the last X minutes.
     * @param string $email
     * @param int $minutes
     * @return int
     */
    public static function getRecentFailedAttempts(string $email, int $minutes = 15): int
    {
        return self::where('email', $email)
            ->where('successful', false)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Get recent failed attempts from an IP address.
     * @param string $ipAddress
     * @param int $minutes
     * @return int
     */
    public static function getRecentFailedAttemptsFromIp(string $ipAddress, int $minutes = 15): int
    {
        return self::where('ip_address', $ipAddress)
            ->where('successful', false)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Check if an email is locked out.
     * @param string $email
     * @return bool
     */
    public static function isLockedOut(string $email): bool
    {
        $maxAttempts = config('auth.login_attempts.max_attempts', 5);
        $lockoutMinutes = config('auth.login_attempts.lockout_minutes', 15);

        return self::getRecentFailedAttempts($email, $lockoutMinutes) >= $maxAttempts;
    }

    /**
     * Check if an IP is locked out.
     * @param string $ipAddress
     * @return bool
     */
    public static function isIpLockedOut(string $ipAddress): bool
    {
        $maxAttempts = config('auth.login_attempts.max_ip_attempts', 20);
        $lockoutMinutes = config('auth.login_attempts.lockout_minutes', 15);

        return self::getRecentFailedAttemptsFromIp($ipAddress, $lockoutMinutes) >= $maxAttempts;
    }

    /**
     * Record a login attempt.
     * @param string $email
     * @param bool $successful
     * @param string|null $failureReason
     * @return self
     */
    public static function record(string $email, bool $successful = false, ?string $failureReason = null): self
    {
        return self::create([
            'email' => $email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'successful' => $successful,
            'failure_reason' => $failureReason,
        ]);
    }

    /**
     * Clear old login attempts (older than retention days).
     * @param int $days
     * @return int
     */
    public static function clearOldAttempts(int $days = 90): int
    {
        return self::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Get lockout time remaining in minutes.
     * @param string $email
     * @return int
     */
    public static function getLockoutTimeRemaining(string $email): int
    {
        $lockoutMinutes = config('auth.login_attempts.lockout_minutes', 15);
        $lastFailedAttempt = self::where('email', $email)
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
}
