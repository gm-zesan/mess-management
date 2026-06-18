<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    /**
     * Log a general action.
     * @param string $action
     * @param string|null $description
     * @param string $status
     * @param int|null $messId
     * @return AuditLog
     */
    public function logAction(
        string $action,
        ?string $description = null,
        string $status = 'success',
        ?int $messId = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'mess_id' => $messId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => $status,
        ]);
    }

    /**
     * Log a model creation.
     * @param Model $model
     * @param array $attributes
     * @param int|null $messId
     * @return AuditLog
     */
    public function logCreated(Model $model, array $attributes = [], ?int $messId = null): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'mess_id' => $messId,
            'action' => 'created',
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'after_values' => $attributes ?: $model->toArray(),
            'description' => class_basename($model) . " #{$model->id} created",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'success',
        ]);
    }

    /**
     * Log a model update.
     * @param Model $model
     * @param array $before
     * @param array $after
     * @param int|null $messId
     * @return AuditLog
     */
    public function logUpdated(Model $model, array $before = [], array $after = [], ?int $messId = null): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'mess_id' => $messId,
            'action' => 'updated',
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'before_values' => $before,
            'after_values' => $after ?: $model->toArray(),
            'description' => class_basename($model) . " #{$model->id} updated",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'success',
        ]);
    }

    /**
     * Log a model deletion.
     * @param Model $model
     * @param array $attributes
     * @param int|null $messId
     * @return AuditLog
     */
    public function logDeleted(Model $model, array $attributes = [], ?int $messId = null): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'mess_id' => $messId,
            'action' => 'deleted',
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'before_values' => $attributes ?: $model->toArray(),
            'description' => class_basename($model) . " #{$model->id} deleted",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'success',
        ]);
    }

    /**
     * Log a login attempt.
     * @param string $email
     * @param bool $successful
     * @param string|null $reason
     * @return AuditLog
     */
    public function logLoginAttempt(string $email, bool $successful = true, ?string $reason = null): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $successful ? 'login' : 'failed_login',
            'description' => $successful ? "Login successful for {$email}" : "Failed login for {$email}: {$reason}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => $successful ? 'success' : 'failed',
        ]);
    }

    /**
     * Log a logout.
     * @return AuditLog
     */
    public function logLogout(): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'logout',
            'description' => 'User logged out',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'success',
        ]);
    }

    /**
     * Log a mess switch (superadmin).
     * @param int $messId
     * @return AuditLog
     */
    public function logMessSwitch(int $messId): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'mess_id' => $messId,
            'action' => 'mess_switch',
            'description' => "Superadmin switched to mess #{$messId}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'success',
        ]);
    }

    /**
     * Log a permission change.
     * @param int $targetUserId
     * @param string $permission
     * @param string $changeType (assigned|revoked)
     * @param int|null $messId
     * @return AuditLog
     */
    public function logPermissionChange(int $targetUserId, string $permission, string $changeType = 'assigned', ?int $messId = null): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'mess_id' => $messId,
            'action' => 'permission_' . $changeType,
            'model_type' => 'User',
            'model_id' => $targetUserId,
            'description' => "Permission '{$permission}' {$changeType} to user #{$targetUserId}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'success',
        ]);
    }

    /**
     * Clean up old audit logs (retention policy).
     * @param int $days
     * @return int
     */
    public function cleanupOldLogs(int $days = 90): int
    {
        return AuditLog::where('created_at', '<', now()->subDays($days))->delete();
    }
}
