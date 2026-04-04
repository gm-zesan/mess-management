<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Mess;
use App\Models\MessUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessUserSeeder extends Seeder
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

        // Get all messes
        $messes = Mess::all();

        if ($messes->isEmpty()) {
            return;
        }

        // Find manager user for first mess
        $manager = $users->firstWhere('name', 'Test User');
        if (!$manager && $users->count() > 0) {
            $manager = $users->first();
        }

        // Get first mess and assign manager
        $mess1 = $messes->first();
        if ($mess1 && $manager) {
            // Add manager to mess with approved status
            MessUser::create([
                'mess_id' => $mess1->id,
                'user_id' => $manager->id,
                'status' => 'approved',
                'invited_by_id' => null,
            ]);
            $manager->assignRole(RoleEnum::MANAGER->value);

            // Add other members to mess1 (only 2 members)
            $mess1Members = $users->whereNotIn('id', [$manager->id])->take(2);
            foreach ($mess1Members as $member) {
                MessUser::create([
                    'mess_id' => $mess1->id,
                    'user_id' => $member->id,
                    'status' => 'approved',
                    'invited_by_id' => $manager->id,
                ]);
                $member->assignRole(RoleEnum::MEMBER->value);
            }
        }

        // Handle second mess if it exists
        if ($messes->count() > 1) {
            $mess2 = $messes->get(1);
            $mess1MemberIds = isset($mess1Members) ? $mess1Members->pluck('id')->toArray() : [];
            $remainingUsers = $users->whereNotIn('id', array_merge([$manager?->id], $mess1MemberIds));
            
            if ($mess2 && $remainingUsers->count() > 0) {
                // Use first remaining user as manager for mess2
                $mess2Manager = $remainingUsers->first();
                
                // Add manager to mess2 with approved status
                MessUser::create([
                    'mess_id' => $mess2->id,
                    'user_id' => $mess2Manager->id,
                    'status' => 'approved',
                    'invited_by_id' => null,
                ]);
                $mess2Manager->assignRole(RoleEnum::MANAGER->value);

                // Add remaining members to mess2
                $mess2Members = $remainingUsers->whereNotIn('id', [$mess2Manager->id])->take(2);
                foreach ($mess2Members as $member) {
                    MessUser::create([
                        'mess_id' => $mess2->id,
                        'user_id' => $member->id,
                        'status' => 'approved',
                        'invited_by_id' => $mess2Manager->id,
                    ]);
                    $member->assignRole(RoleEnum::MEMBER->value);
                }
            }
        }
    }
}
