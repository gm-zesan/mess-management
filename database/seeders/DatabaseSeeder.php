<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run role and permission seeders first
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);

        // Run data seeders
        $this->call(UserSeeder::class);
        $this->call(MonthSeeder::class);
        $this->call(MealSeeder::class);
        $this->call(ExpenseSeeder::class);
        $this->call(DepositSeeder::class);
    }
}
