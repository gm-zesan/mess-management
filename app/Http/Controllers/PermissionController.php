<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display all roles with their permissions.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only superadmin can manage permissions
        if (!$user->hasRole(RoleEnum::SUPERADMIN->value)) {
            abort(403, 'Only superadmins can manage permissions.');
        }

        $roles = Role::with('permissions')->where('name', '!=', 'superadmin')->get();
        $allPermissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0]; // Group by module name
        });

        return view('permissions.index', compact('roles', 'allPermissions'));
    }

    /**
     * Assign permission to role.
     */
    public function assignPermission(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user->hasRole(RoleEnum::SUPERADMIN->value)) {
            abort(403, 'Only superadmins can manage permissions.');
        }

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $permission = Permission::findOrFail($validated['permission_id']);

        if (!$role->hasPermissionTo($permission)) {
            $role->givePermissionTo($permission);
        }

        return redirect()->route('permissions.index')->with('success', "Permission '{$permission->name}' assigned to role '{$role->name}'.");
    }

    /**
     * Revoke permission from role.
     */
    public function revokePermission(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user->hasRole(RoleEnum::SUPERADMIN->value)) {
            abort(403, 'Only superadmins can manage permissions.');
        }

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $permission = Permission::findOrFail($validated['permission_id']);

        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
        }

        return redirect()->route('permissions.index')->with('success', "Permission '{$permission->name}' revoked from role '{$role->name}'.");
    }
}
