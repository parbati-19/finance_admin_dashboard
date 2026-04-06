<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): UserCollection
    {
        $query = User::with('roles');

        if ($request->input('status') === 'deleted') {
            $query->onlyTrashed();
        } elseif ($request->input('status') === 'active') {
            $query->where('status', 1);
        } elseif ($request->input('status') === 'inactive') {
            $query->where('status', 0);
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', 'like', '%' . $request->input('role') . '%'));
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        return new UserCollection($query->paginate($request->integer('per_page', 10)));
    }

    public function show(User $user): JsonResponse
    {
        $user->load(['roles', 'detail']);

        return response()->json(['user' => new UserResource($user)]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'status' => $validated['status'],
        ]);

        $user->assignRole($validated['role']);
        $user->load('roles');

        return response()->json([
            'message' => 'User created successfully.',
            'user' => new UserResource($user),
        ], 201);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();
        $user->update($validated);

        if (!empty($validated['password'])) {
            $user->update(['password' => bcrypt($validated['password'])]);
        }

        if(!empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }
        $user->load('roles');

        return response()->json([
            'message' => 'User updated successfully.',
            'user' => new UserResource($user),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    public function restore(int $id): JsonResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        $user->load('roles');

        return response()->json([
            'message' => 'User restored successfully.',
            'user' => new UserResource($user),
        ]);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();

        return response()->json(['message' => 'User permanently deleted.']);
    }
}