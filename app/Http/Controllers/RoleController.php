<?php

namespace App\Http\Controllers;

use App\Http\Requests\Roles\StoreRoleRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        return Inertia::render('Roles/Index', [
            'roles' => RoleResource::collection($query->paginate(10)),
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        Role::create($request->validated());

        return redirect()->route('roles.index')->with('success', 'Role created successfully');
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->validated());

        return redirect()->route('roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}
