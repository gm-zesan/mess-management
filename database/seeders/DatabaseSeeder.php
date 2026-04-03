<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Member;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->call(RoleSeeder::class);

        // Seed test members with authentication credentials
        Member::factory()->create([
            'name' => 'Ashraf Ahmed',
            'status' => 'active',
            'email' => 'ashraf@example.com',
            'password' => bcrypt('password'),
        ]);

        Member::factory()->create([
            'name' => 'Karim Khan',
            'status' => 'active',
            'email' => 'karim@example.com',
            'password' => bcrypt('password'),
        ]);

        Member::factory()->create([
            'name' => 'Fatima Hassan',
            'status' => 'active',
            'email' => 'fatima@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
