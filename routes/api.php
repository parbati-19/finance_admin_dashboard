<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TransactionAnalyticsController;
use App\Http\Controllers\Api\TransactionRecordController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserTableController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api and use Sanctum token authentication.
|
*/

// Public
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Authenticated
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view dashboard');

    // Transactions
    Route::middleware('permission:view transactions')->group(function () {
        Route::apiResource('transaction-records', TransactionRecordController::class)->names('api.transaction-records');
        Route::get('transaction-records-categories', [TransactionRecordController::class, 'categories']);
        Route::get('transaction-analytics', [TransactionAnalyticsController::class, 'index']);
    });

    // Users & Access Control
    Route::middleware('permission:view users')->group(function () {
        // Users
        Route::apiResource('users', UserController::class)->names('api.users');
        Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('api.users.restore');
        Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('api.users.force-delete');

        // User table (legacy component endpoint)
        Route::get('user-table', [UserTableController::class, 'index']);
        Route::delete('user-table/{id}', [UserTableController::class, 'destroy']);
        Route::patch('user-table/{id}/restore', [UserTableController::class, 'restore']);
        Route::delete('user-table/{id}/force-delete', [UserTableController::class, 'forceDelete']);

        // Roles
        Route::apiResource('roles', RoleController::class)->names('api.roles');
        Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('api.roles.permissions');

        // Permissions
        Route::apiResource('permissions', PermissionController::class)->names('api.permissions');
    });
});