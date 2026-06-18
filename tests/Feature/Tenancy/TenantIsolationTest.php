<?php

namespace Tests\Feature\Tenancy;

use App\Models\Meal;
use App\Models\Mess;
use App\Models\MessUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user1;
    protected User $user2;
    protected Mess $mess1;
    protected Mess $mess2;
    protected Meal $meal1;
    protected Meal $meal2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = User::factory()->create(['email' => 'user1@example.com']);
        $this->user2 = User::factory()->create(['email' => 'user2@example.com']);

        $this->mess1 = Mess::factory()->create(['name' => 'Mess 1']);
        $this->mess2 = Mess::factory()->create(['name' => 'Mess 2']);

        MessUser::create([
            'user_id' => $this->user1->id,
            'mess_id' => $this->mess1->id,
            'status' => 'approved',
        ]);

        MessUser::create([
            'user_id' => $this->user2->id,
            'mess_id' => $this->mess2->id,
            'status' => 'approved',
        ]);

        $this->meal1 = Meal::factory()->create(['mess_id' => $this->mess1->id]);
        $this->meal2 = Meal::factory()->create(['mess_id' => $this->mess2->id]);
    }

    #[Test]
    public function user_can_only_see_meals_from_their_mess(): void
    {
        $this->actingAs($this->user1);
        $this->session(['mess_id' => $this->mess1->id]);

        $meals = Meal::all();

        $this->assertEquals(1, $meals->count());
        $this->assertTrue($meals->first()->id === $this->meal1->id);
    }

    #[Test]
    public function user_cannot_see_meals_from_other_mess(): void
    {
        $this->actingAs($this->user1);
        $this->session(['mess_id' => $this->mess1->id]);

        $meals = Meal::all();

        $this->assertFalse($meals->contains($this->meal2));
    }

    #[Test]
    public function different_users_see_different_meals(): void
    {
        $this->actingAs($this->user1);
        $this->session(['mess_id' => $this->mess1->id]);
        $user1Meals = Meal::all();

        $this->actingAs($this->user2);
        $this->session(['mess_id' => $this->mess2->id]);
        $user2Meals = Meal::all();

        $this->assertNotEquals($user1Meals->pluck('id')->all(), $user2Meals->pluck('id')->all());
    }

    #[Test]
    public function unauthenticated_user_cannot_access_meals(): void
    {
        $meals = Meal::all();

        $this->assertEmpty($meals);
    }

    #[Test]
    public function user_with_multiple_messes_sees_only_active_mess(): void
    {
        MessUser::create([
            'user_id' => $this->user1->id,
            'mess_id' => $this->mess2->id,
            'status' => 'approved',
        ]);

        $this->actingAs($this->user1);
        $this->session(['mess_id' => $this->mess1->id]);

        $meals = Meal::all();

        $this->assertEquals(1, $meals->count());
        $this->assertTrue($meals->first()->id === $this->meal1->id);

        $this->session(['mess_id' => $this->mess2->id]);

        $meals = Meal::all();

        $this->assertEquals(1, $meals->count());
        $this->assertTrue($meals->first()->id === $this->meal2->id);
    }
}
