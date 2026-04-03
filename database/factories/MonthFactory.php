<?php

namespace Database\Factories;

use App\Models\Month;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Month>
 */
class MonthFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-3 months', 'now');
        $endDate = (clone $startDate)->modify('+1 month')->modify('-1 day');

        return [
            'name' => $startDate->format('F Y'),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'status' => 'inactive',
            'closed_at' => null,
        ];
    }

    /**
     * Indicate that the month is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the month is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
            'closed_at' => now(),
        ]);
    }
}
