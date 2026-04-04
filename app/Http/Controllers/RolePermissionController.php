<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::all(['id', 'name']);
        $permissions = Permission::all(['id', 'name']);

        $selectedRole = null;
        $rolePermissions = [];

        if ($request->filled('role_id')) {
            $selectedRole = Role::findById($request->input('role_id'));
            $rolePermissions = $selectedRole->permissions->pluck('id')->toArray();
        }

        return Inertia::render('Roles/RolePermission', [
            'roles' => $roles,
            'permissions' => $permissions,
            'selectedRoleId' => $selectedRole?->id,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'role_id' => 'required|integer|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role = Role::findById($data['role_id']);
        $permissions = Permission::whereIn('id', $data['permissions'])->get();
        $role->syncPermissions($permissions);

        return redirect()->route('role-permissions.index', ['role_id' => $role->id])
            ->with('success', 'Permissions updated for role: ' . $role->name);
    }
}
