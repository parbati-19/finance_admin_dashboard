<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\StoreRoleRequest;
use App\Http\Requests\Roles\SyncRolePermissionsRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Role::with('permissions');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        return response()->json([
            'roles' => RoleResource::collection($query->paginate($request->integer('per_page', 10))),
        ]);
    }

    public function show(Role $role): JsonResponse
    {
        $role->load('permissions');

        return response()->json(['role' => new RoleResource($role)]);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = Role::create([
            ...$request->validated(),
            'guard_name' => $request->input('guard_name', 'web'),
        ]);

        return response()->json([
            'message' => 'Role created succesfully.',
            'role' => new RoleResource($role),
        ], 201);
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role->update($request->validated());

        return response()->json([
            'message' => 'Role updated succesfully.',
            'role' => new RoleResource($role),
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        $assignedCount = DB::table('model_has_roles')
            ->where('role_id', $role->id)
            ->count();

        if ($assignedCount > 0) {
            return response()->json([
                'message' => 'Cannot delete a role that is still assigned to users.',
            ], 422);
        }

        $role->delete();

        return response()->json(['message' => 'Role deleted succesfully.']);
    }

    public function syncPermissions(SyncRolePermissionsRequest $request, Role $role): JsonResponse
    {
        $data = $request->validated();

        $role->syncPermissions($data['permissions'] ?? []);
        $role->load('permissions');

        return response()->json([
            'message' => 'Permissions synced successfully.',
            'role' => new RoleResource($role),
        ]);
    }
}
