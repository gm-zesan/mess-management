<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Mess;
use App\Models\MessUser;
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

        // Create first mess - "Main Mess"
        $mess1 = Mess::create([
            'name' => 'Main Mess',
            'description' => 'Primary shared mess for the group',
            'join_code' => strtoupper(Str::random(8)),
            'creator_id' => $users->first()->id,
        ]);

        // Find manager user and add to mess with manager role
        $manager = $users->firstWhere('name', 'Test User');
        if ($manager) {
            MessUser::create([
                'mess_id' => $mess1->id,
                'user_id' => $manager->id,
                'status' => 'approved',
                'invited_by_id' => null,
            ]);
            $manager->assignRole(RoleEnum::MANAGER->value);
        }

        // Add other members to mess1 with approved status (only 2 members to mess1)
        $mess1Members = $users->whereNotIn('id', [$manager?->id])->take(2);
        foreach ($mess1Members as $member) {
            MessUser::create([
                'mess_id' => $mess1->id,
                'user_id' => $member->id,
                'status' => 'approved',
                'invited_by_id' => $manager?->id,
            ]);
            $member->assignRole(RoleEnum::MEMBER->value);
        }

        // Create second mess - "Side Mess"
        $mess2 = Mess::create([
            'name' => 'Side Mess',
            'description' => 'Secondary mess for additional group',
            'join_code' => strtoupper(Str::random(8)),
            'creator_id' => $users->get(2)?->id ?? $users->first()->id,
        ]);

        // Add remaining members to mess2 only (not already assigned to mess1)
        $mess2Members = $users->whereNotIn('id', array_merge([$manager?->id], $mess1Members->pluck('id')->toArray()))->take(2);
        foreach ($mess2Members as $member) {
            MessUser::create([
                'mess_id' => $mess2->id,
                'user_id' => $member->id,
                'status' => 'approved',
                'invited_by_id' => null,
            ]);
            $member->assignRole(RoleEnum::MEMBER->value);
        }
    }
}
