<?php

namespace Tests\Feature\Audit;

use App\Models\AuditLog;
use App\Models\Meal;
use App\Models\Mess;
use App\Models\MessUser;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuditLoggingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Mess $mess;
    protected AuditLogService $auditService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->mess = Mess::factory()->create();

        MessUser::create([
            'user_id' => $this->user->id,
            'mess_id' => $this->mess->id,
            'status' => 'approved',
        ]);

        $this->auditService = app(AuditLogService::class);

        $this->actingAs($this->user);
        $this->session(['mess_id' => $this->mess->id]);
    }

    #[Test]
    public function audit_log_is_created_for_general_action(): void
    {
        $this->auditService->logAction(
            'test_action',
            'Test action description',
            'success',
            $this->mess->id
        );

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'test_action',
            'description' => 'Test action description',
            'status' => 'success',
            'user_id' => $this->user->id,
            'mess_id' => $this->mess->id,
        ]);
    }

    #[Test]
    public function audit_log_captures_ip_address(): void
    {
        $this->auditService->logAction('test', 'test', 'success', $this->mess->id);

        $log = AuditLog::latest()->first();

        $this->assertNotNull($log->ip_address);
    }

    #[Test]
    public function audit_log_is_created_on_model_creation(): void
    {
        $meal = Meal::factory()->create(['mess_id' => $this->mess->id]);

        $this->auditService->logCreated($meal, $meal->toArray(), $this->mess->id);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'model_type' => Meal::class,
            'model_id' => $meal->id,
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function audit_log_captures_before_values_on_update(): void
    {
        $meal = Meal::factory()->create(['mess_id' => $this->mess->id, 'description' => 'Original']);

        $before = $meal->toArray();
        $meal->update(['description' => 'Updated']);
        $after = $meal->fresh()->toArray();

        $this->auditService->logUpdated($meal, $before, $after, $this->mess->id);

        $log = AuditLog::where('action', 'updated')->latest()->first();

        $this->assertNotNull($log->before_values);
        $this->assertNotNull($log->after_values);
    }

    #[Test]
    public function audit_log_is_created_on_model_deletion(): void
    {
        $meal = Meal::factory()->create(['mess_id' => $this->mess->id]);
        $mealData = $meal->toArray();

        $this->auditService->logDeleted($meal, $mealData, $this->mess->id);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'deleted',
            'model_type' => Meal::class,
            'model_id' => $meal->id,
        ]);
    }

    #[Test]
    public function login_audit_is_created(): void
    {
        $this->auditService->logLoginAttempt('user@example.com', true, 'Successful login');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'login_attempt',
            'description' => 'Successful login',
        ]);
    }

    #[Test]
    public function failed_login_audit_is_created(): void
    {
        $this->auditService->logLoginAttempt('user@example.com', false, 'Invalid credentials');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'login_attempt',
            'description' => 'Invalid credentials',
            'status' => 'failed',
        ]);
    }

    #[Test]
    public function logout_audit_is_created(): void
    {
        $this->auditService->logLogout();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'logout',
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function mess_switch_audit_is_created(): void
    {
        $this->auditService->logMessSwitch($this->mess->id);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'mess_switch',
            'mess_id' => $this->mess->id,
        ]);
    }

    #[Test]
    public function audit_logs_can_be_filtered_by_user(): void
    {
        $other_user = User::factory()->create();

        $this->auditService->logAction('action1', 'test', 'success', $this->mess->id);

        $this->actingAs($other_user);
        $this->auditService->logAction('action2', 'test', 'success', $this->mess->id);

        $logs = AuditLog::forUser($this->user->id)->get();

        $this->assertEquals(1, $logs->count());
        $this->assertEquals($this->user->id, $logs->first()->user_id);
    }

    #[Test]
    public function audit_logs_can_be_filtered_by_mess(): void
    {
        $other_mess = Mess::factory()->create();

        $this->auditService->logAction('action1', 'test', 'success', $this->mess->id);
        $this->auditService->logAction('action2', 'test', 'success', $other_mess->id);

        $logs = AuditLog::forMess($this->mess->id)->get();

        $this->assertEquals(1, $logs->count());
        $this->assertEquals($this->mess->id, $logs->first()->mess_id);
    }

    #[Test]
    public function audit_logs_can_be_filtered_by_action(): void
    {
        $this->auditService->logAction('action1', 'test', 'success', $this->mess->id);
        $this->auditService->logAction('action2', 'test', 'success', $this->mess->id);

        $logs = AuditLog::forAction('action1')->get();

        $this->assertEquals(1, $logs->count());
        $this->assertEquals('action1', $logs->first()->action);
    }

    #[Test]
    public function old_audit_logs_are_cleaned_up(): void
    {
        $retentionDays = 90;

        AuditLog::create([
            'user_id' => $this->user->id,
            'mess_id' => $this->mess->id,
            'action' => 'old_action',
            'description' => 'Old log',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test',
            'status' => 'success',
            'created_at' => now()->subDays($retentionDays + 1),
        ]);

        AuditLog::create([
            'user_id' => $this->user->id,
            'mess_id' => $this->mess->id,
            'action' => 'recent_action',
            'description' => 'Recent log',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test',
            'status' => 'success',
        ]);

        $this->auditService->cleanupOldLogs($retentionDays);

        $this->assertDatabaseMissing('audit_logs', ['action' => 'old_action']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'recent_action']);
    }
}
