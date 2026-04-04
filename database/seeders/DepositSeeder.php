<?php

namespace Database\Seeders;

use App\Models\Deposit;
use App\Models\Month;
use App\Models\Mess;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DepositSeeder extends Seeder
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

        // Create sample deposits for April
        $deposits = [
            [
                'mess_id' => $mess->id,
                'month_id' => $month->id,
                'user_id' => $members->first()->id,
                'date' => Carbon::createFromDate(2026, 4, 1),
                'amount' => 5000,
            ],
            [
                'mess_id' => $mess->id,
                'month_id' => $month->id,
                'user_id' => $members->get(1)->id ?? $members->first()->id,
                'date' => Carbon::createFromDate(2026, 4, 1),
                'amount' => 5000,
            ],
            [
                'mess_id' => $mess->id,
                'month_id' => $month->id,
                'user_id' => $members->get(2)->id ?? $members->first()->id,
                'date' => Carbon::createFromDate(2026, 4, 1),
                'amount' => 5000,
            ],
            [
                'mess_id' => $mess->id,
                'month_id' => $month->id,
                'user_id' => $members->first()->id,
                'date' => Carbon::createFromDate(2026, 4, 15),
                'amount' => 2000,
            ],
        ];

        foreach ($deposits as $deposit) {
            Deposit::create($deposit);
        }
    }
}
