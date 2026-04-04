<?php

namespace Database\Seeders;

use App\Models\Month;
use App\Models\Mess;
use App\Enums\MonthStatusEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MonthSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mess = Mess::first();
        
        if (!$mess) {
            return;
        }

        Month::create([
            'mess_id' => $mess->id,
            'name' => 'April 2026',
            'start_date' => '2026-04-01',
            'end_date' => '2026-04-30',
            'status' => MonthStatusEnum::ACTIVE,
            'closed_at' => null,
        ]);
    }
}
