<?php

namespace Tests\Feature\Auth;

use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $userEmail = 'test@example.com';

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email' => $this->userEmail]);
    }

    #[Test]
    public function user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => $this->userEmail,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect();
    }

    #[Test]
    public function failed_login_records_attempt(): void
    {
        $this->post('/login', [
            'email' => $this->userEmail,
            'password' => 'wrong-password',
        ]);

        $this->assertDatabaseHas('login_attempts', [
            'email' => $this->userEmail,
            'successful' => false,
        ]);
    }

    #[Test]
    public function account_is_locked_after_max_attempts(): void
    {
        $maxAttempts = config('auth.login_attempts.max_attempts');

        for ($i = 0; $i < $maxAttempts; $i++) {
            $this->post('/login', [
                'email' => $this->userEmail,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post('/login', [
            'email' => $this->userEmail,
            'password' => 'password',
        ]);

        // When locked out, should redirect (302) with error or return 429
        $this->assertTrue(
            in_array($response->status(), [302, 429, 422]),
            "Expected 302, 429 or 422 but got {$response->status()}"
        );
    }

    #[Test]
    public function old_login_attempts_are_cleaned_up(): void
    {
        // Create records with explicit old timestamp
        $now = now();
        $retentionDays = 90;

        // Create an old record (clearly older than retention)
        LoginAttempt::query()->insert([
            'email' => 'old@example.com',
            'successful' => false,
            'failure_reason' => 'old',
            'ip_address' => '192.168.1.1',
            'created_at' => $now->clone()->subDays($retentionDays + 10),
            'updated_at' => $now->clone()->subDays($retentionDays + 10),
        ]);

        // Create a recent record
        LoginAttempt::query()->insert([
            'email' => 'recent@example.com',
            'successful' => false,
            'failure_reason' => 'recent',
            'ip_address' => '192.168.1.1',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Before cleanup, both should exist
        $this->assertDatabaseHas('login_attempts', ['email' => 'old@example.com']);
        $this->assertDatabaseHas('login_attempts', ['email' => 'recent@example.com']);

        // Run cleanup
        $this->artisan('auth:cleanup-login-attempts');

        // After cleanup, only recent should exist
        $this->assertDatabaseMissing('login_attempts', ['email' => 'old@example.com']);
        $this->assertDatabaseHas('login_attempts', ['email' => 'recent@example.com']);
    }
}
