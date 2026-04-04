<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\Api\UserTableController;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::put('role-switch', [\App\Http\Controllers\RoleSwitchController::class, 'update'])->name('role-switch.update');

    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard')
        ->middleware('permission:view dashboard');

    Route::middleware('permission:view transactions')->group(function () {
        Route::resource('transaction-records', \App\Http\Controllers\TransactionRecordController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('transaction-analytics', [\App\Http\Controllers\TransactionAnalyticsController::class, 'index'])->name('transaction-analytics.index');
    });

    Route::middleware('permission:view users')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::patch('users/{id}/restore', [\App\Http\Controllers\UserController::class, 'restore'])->name('users.restore');
        Route::delete('users/{id}/force-delete', [\App\Http\Controllers\UserController::class, 'forceDelete'])->name('users.force-delete');
        Route::resource('roles', \App\Http\Controllers\RoleController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('permissions', \App\Http\Controllers\PermissionController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('role-permissions', [\App\Http\Controllers\RolePermissionController::class, 'index'])->name('role-permissions.index');
        Route::put('role-permissions', [\App\Http\Controllers\RolePermissionController::class, 'update'])->name('role-permissions.update');
    });
});
require __DIR__ . '/settings.php';
