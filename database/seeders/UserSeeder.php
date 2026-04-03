<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
        ]);
        $superAdmin->assignRole(RoleEnum::SUPERADMIN);

        $manager = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $manager->assignRole(RoleEnum::MANAGER);

        $member1 = User::factory()->create([
            'name' => 'Ashraf Ahmed',
            'email' => 'ashraf@example.com',
            'password' => bcrypt('password'),
        ]);
        $member1->assignRole(RoleEnum::MEMBER);

        $member2 = User::factory()->create([
            'name' => 'Karim Khan',
            'email' => 'karim@example.com',
            'password' => bcrypt('password'),
        ]);
        $member2->assignRole(RoleEnum::MEMBER);

        $member3 = User::factory()->create([
            'name' => 'Fatima Hassan',
            'email' => 'fatima@example.com',
            'password' => bcrypt('password'),
        ]);
        $member3->assignRole(RoleEnum::MEMBER);
    }
}
