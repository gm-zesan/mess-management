<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Month;
use App\Models\Mess;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $month = Month::where('name', 'April 2026')->first();
        $mess = Mess::first();
        $members = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['member', 'manager']);
        })->get();

        if (!$month || !$mess || $members->isEmpty()) {
            return;
        }

        // Create sample expenses for April
        $expenses = [
            [
                'mess_id' => $mess->id,
                'month_id' => $month->id,
                'user_id' => $members->first()->id,
                'date' => Carbon::createFromDate(2026, 4, 1),
                'category' => 'Grocery',
                'note' => 'Rice and vegetables',
                'amount' => 1500,
            ],
            [
                'mess_id' => $mess->id,
                'month_id' => $month->id,
                'user_id' => $members->get(1)->id ?? $members->first()->id,
                'date' => Carbon::createFromDate(2026, 4, 2),
                'category' => 'Utility',
                'note' => 'Cooking gas',
                'amount' => 800,
            ],
            [
                'mess_id' => $mess->id,
                'month_id' => $month->id,
                'user_id' => $members->first()->id,
                'date' => Carbon::createFromDate(2026, 4, 5),
                'category' => 'Grocery',
                'note' => 'Chicken and spices',
                'amount' => 2000,
            ],
            [
                'mess_id' => $mess->id,
                'month_id' => $month->id,
                'user_id' => $members->get(1)->id ?? $members->first()->id,
                'date' => Carbon::createFromDate(2026, 4, 10),
                'category' => 'Utility',
                'note' => 'Electricity bill',
                'amount' => 1200,
            ],
        ];

        foreach ($expenses as $expense) {
            Expense::create($expense);
        }
    }
}
