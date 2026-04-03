<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create all permissions from enum
        foreach (PermissionEnum::cases() as $permission) {
            Permission::firstOrCreate(['name' => $permission->value, 'guard_name' => 'web']);
        }

        // Get roles
        $superAdminRole = Role::firstOrCreate(['name' => RoleEnum::SUPERADMIN->value, 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => RoleEnum::MANAGER->value, 'guard_name' => 'web']);
        $memberRole = Role::firstOrCreate(['name' => RoleEnum::MEMBER->value, 'guard_name' => 'web']);

        // Superadmin: has all permissions
        $superAdminRole->syncPermissions(Permission::all());

        // Manager: can do everything except delete months and superadmin management
        $managerPermissions = Permission::whereNotIn('name', [
            PermissionEnum::MONTHS_DELETE->value,
        ])->get();
        $managerRole->syncPermissions($managerPermissions);

        // Member: limited permissions (read-only)
        $memberPermissions = Permission::whereIn('name', [
            PermissionEnum::DASHBOARD_VIEW->value,
            PermissionEnum::MEMBERS_VIEW->value,
            PermissionEnum::EXPENSES_VIEW->value,
            PermissionEnum::DEPOSITS_VIEW->value,
            PermissionEnum::MONTHS_VIEW->value,
            PermissionEnum::REPORTS_ALL_MONTHS->value,
            PermissionEnum::REPORTS_VIEW->value,
        ])->get();
        $memberRole->syncPermissions($memberPermissions);
    }
}
