<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view dashboard',
            'view transactions',
            'view users',
            'view roles',
            'view permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions($permissions);

        $analyst = Role::firstOrCreate(['name' => 'Analyst']);
        $analyst->syncPermissions(['view transactions']);

        $visitor = Role::firstOrCreate(['name' => 'Visitor']);
        $visitor->syncPermissions(['view dashboard']);

        // Create test user with Admin role
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ],
        );
        $user->assignRole('Admin');
    }
}
