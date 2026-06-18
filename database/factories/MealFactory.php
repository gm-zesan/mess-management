<?php

namespace Database\Factories;

use App\Models\Meal;
use App\Models\Mess;
use App\Models\Month;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'mess_id' => Mess::factory(),
            'user_id' => User::factory(),
            'month_id' => Month::factory(),
            'date' => $this->faker->date(),
            'breakfast_count' => $this->faker->randomFloat(2, 0, 3),
            'lunch_count' => $this->faker->randomFloat(2, 0, 3),
            'dinner_count' => $this->faker->randomFloat(2, 0, 3),
        ];
    }
}

