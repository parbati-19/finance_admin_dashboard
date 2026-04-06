<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permissions\StorePermissionRequest;
use App\Http\Requests\Permissions\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Permission::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        return response()->json([
            'permissions' => PermissionResource::collection($query->paginate($request->integer('per_page', 10))),
        ]);
    }

    public function show(Permission $permission): JsonResponse
    {
        return response()->json(['permission' => new PermissionResource($permission)]);
    }

    public function store(StorePermissionRequest $request): JsonResponse
    {
        $permission = Permission::create([
            ...$request->validated(),
            'guard_name' => $request->input('guard_name', 'web'),
        ]);

        return response()->json([
            'message' => 'Permission created.',
            'permission' => new PermissionResource($permission),
        ], 201);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        $permission->update($request->validated());

        return response()->json([
            'message' => 'Permission updated.',
            'permission' => new PermissionResource($permission),
        ]);
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();

        return response()->json(['message' => 'Permission deleted.']);
    }
}
