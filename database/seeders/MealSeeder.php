<?php

namespace Database\Seeders;

use App\Models\Meal;
use App\Models\Month;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MealSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $month = Month::where('name', 'April 2026')->first();

        if (!$month) {
            return;
        }

        // Create sample meals for April
        $meals = [
            [
                'month_id' => $month->id,
                'user_id' => 2, // Test User (Manager)
                'date' => Carbon::createFromDate(2026, 4, 1),
                'meal_count' => 3,
            ],
            [
                'month_id' => $month->id,
                'user_id' => 3, // Ashraf Ahmed
                'date' => Carbon::createFromDate(2026, 4, 2),
                'meal_count' => 3,
            ],
            [
                'month_id' => $month->id,
                'user_id' => 4, // Karim Khan
                'date' => Carbon::createFromDate(2026, 4, 3),
                'meal_count' => 2,
            ],
            [
                'month_id' => $month->id,
                'user_id' => 5, // Fatima Hassan
                'date' => Carbon::createFromDate(2026, 4, 4),
                'meal_count' => 3,
            ],
            [
                'month_id' => $month->id,
                'user_id' => 2, // Test User (Manager)
                'date' => Carbon::createFromDate(2026, 4, 5),
                'meal_count' => 3,
            ],
        ];

        foreach ($meals as $meal) {
            Meal::create($meal);
        }
    }
}
