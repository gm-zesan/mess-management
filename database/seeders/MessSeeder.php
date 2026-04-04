<?php

namespace Database\Seeders;

use App\Models\Mess;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MessSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users except superadmin
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['manager', 'member']);
        })->get();

        if ($users->isEmpty()) {
            return;
        }

        // Find manager user for first mess
        $manager = $users->firstWhere('name', 'Test User');
        if (!$manager && $users->count() > 0) {
            $manager = $users->first();
        }

        // Create first mess - "Main Mess" with manager_id
        Mess::create([
            'name' => 'Main Mess',
            'description' => 'Primary shared mess for the group',
            'join_code' => strtoupper(Str::random(8)),
            'creator_id' => $manager->id,
            'manager_id' => $manager->id,
        ]);

        // Get remaining members for mess2
        $mess1Members = $users->whereNotIn('id', [$manager?->id])->take(2);
        $remainingForMess2Manager = $users->whereNotIn('id', array_merge([$manager?->id], $mess1Members->pluck('id')->toArray()));
        
        if ($remainingForMess2Manager->count() > 0) {
            // Use first remaining user as manager for mess2
            $mess2Manager = $remainingForMess2Manager->first();
            
            // Create second mess - "Side Mess" with manager_id
            Mess::create([
                'name' => 'Side Mess',
                'description' => 'Secondary mess for additional group',
                'join_code' => strtoupper(Str::random(8)),
                'creator_id' => $mess2Manager->id,
                'manager_id' => $mess2Manager->id,
            ]);
        }
    }
}
