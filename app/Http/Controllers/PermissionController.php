<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permissions\StorePermissionRequest;
use App\Http\Requests\Permissions\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        return Inertia::render('Permissions/Index', [
            'permissions' => PermissionResource::collection($query->paginate(10)),
        ]);
    }

    public function store(StorePermissionRequest $request)
    {
        Permission::create($request->validated());

        return redirect()->route('permissions.index')->with('success', 'Permission created');
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());

        return redirect()->route('permissions.index')->with('success', 'Permission updated');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted');
    }
}
