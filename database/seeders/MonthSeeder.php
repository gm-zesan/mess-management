<?php

namespace Database\Seeders;

use App\Models\Month;
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
        Month::create([
            'name' => 'April 2026',
            'start_date' => '2026-04-01',
            'end_date' => '2026-04-30',
            'status' => MonthStatusEnum::ACTIVE,
            'closed_at' => null,
        ]);
    }
}
