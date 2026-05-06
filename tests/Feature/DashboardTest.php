<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    Permission::create(['name' => 'view dashboard']);

    $user = User::factory()->create();

    $user->givePermissionTo('view dashboard');

    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});