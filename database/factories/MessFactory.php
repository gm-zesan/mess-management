<?php

namespace Database\Factories;

use App\Models\Mess;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mess>
 */
class MessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $creator = User::factory()->create();

        return [
            'name' => $this->faker->word . ' Mess',
            'description' => $this->faker->sentence,
            'join_code' => strtoupper($this->faker->unique()->bothify('????####')),
            'creator_id' => $creator->id,
            'manager_id' => $creator->id,
        ];
    }
}
