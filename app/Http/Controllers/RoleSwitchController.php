<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleSwitchController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user = $request->user();
        $role = Role::findByName($request->input('role'));

        $user->syncRoles([$role]);

        // Clear cached permissions so the new role takes effect immediately
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Redirect based on what the new role can access
        $permissions = $role->permissions->pluck('name')->toArray();

        if (in_array('view dashboard', $permissions)) {
            return redirect()->route('dashboard');
        }

        if (in_array('view transactions', $permissions)) {
            return redirect()->route('transaction-records.index');
        }

        return redirect()->route('dashboard');
    }
}
