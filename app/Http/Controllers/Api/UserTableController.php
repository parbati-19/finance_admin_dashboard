<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserTableController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::withTrashed()->with('roles')->get();

        $columns = [
            ['key' => 'id', 'header' => '#'],
            ['key' => 'name', 'header' => 'Name'],
            ['key' => 'email', 'header' => 'Email'],
            ['key' => 'role', 'header' => 'Role'],
            ['key' => 'actions', 'header' => 'Actions'],
            ['key' => 'created_at', 'header' => 'Created At'],
            ['key' => 'deleted', 'header' => 'Deleted'],
        ];

        return response()->json([
            'columns' => $columns,
            'data' => UserResource::collection($users),
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        $user->delete();

        return response()->json(['message' => 'User deleted.']);
    }

    public function restore(Request $request, $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        if (method_exists($user, 'restore')) {
            $user->restore();
            return response()->json(['message' => 'User restored.']);
        }

        return response()->json(['message' => 'Restore not available.'], 400);
    }

    public function forceDelete(Request $request, $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        if (method_exists($user, 'forceDelete')) {
            $user->forceDelete();
            return response()->json(['message' => 'User permanently deleted.']);
        }

        return response()->json(['message' => 'Force delete not available.'], 400);
    }
}
