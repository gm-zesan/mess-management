<?php

namespace Database\Seeders;

use App\Models\Meal;
use App\Models\Month;
use App\Models\Mess;
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
        $mess = Mess::first();

        if (!$month || !$mess) {
            return;
        }

        // Create sample meals for April with breakfast, lunch, dinner counts
        $meals = [
            [
                'mess_id' => $mess->id,
                'month_id' => $month->id,
                'user_id' => 2, // Test User (Manager)
                'date' => Carbon::createFromDate(2026, 4, 1),
                'breakfast_count' => 1,
                'lunch_count' => 1,
                'dinner_count' => 1,
            ],
            [
                'mess_id' => $mess->id,
                'month_id' => $month->id,
                'user_id' => 3, // Ashraf Ahmed
                'date' => Carbon::createFromDate(2026, 4, 2),
                'breakfast_count' => 1,
                'lunch_count' => 1,
                'dinner_count' => 1,
            ],
            [
                'mess_id' => $mess->id,
                'month_id' => $month->id,
                'user_id' => 4, // Karim Khan
                'date' => Carbon::createFromDate(2026, 4, 3),
                'breakfast_count' => 1,
                'lunch_count' => 1,
                'dinner_count' => 0,
            ],
            [
                'mess_id' => $mess->id,
                'month_id' => $month->id,
                'user_id' => 5, // Fatima Hassan
                'date' => Carbon::createFromDate(2026, 4, 4),
                'breakfast_count' => 0.5,
                'lunch_count' => 1,
                'dinner_count' => 1,
            ],
            [
                'mess_id' => $mess->id,
                'month_id' => $month->id,
                'user_id' => 2, // Test User (Manager)
                'date' => Carbon::createFromDate(2026, 4, 5),
                'breakfast_count' => 1,
                'lunch_count' => 0.5,
                'dinner_count' => 1,
            ],
        ];

        foreach ($meals as $meal) {
            Meal::create($meal);
        }
    }
}
