<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // User Management (global)
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Agent-specific Management
            'agents.view',
            'agents.create',
            'agents.edit',
            'agents.delete',

            // Role & Permission Management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Settings Section
            'settings.view',
            'settings.manage',

            // Ticket System
            'tickets.view',
            'tickets.reply',

            // Logs
            // 'logs.view.fedex',
            // 'logs.view.tickets',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm, 'guard_name' => 'web']
            );
        }

        // 2. Create roles
        $superadmin = Role::firstOrCreate(
            ['name' => 'superadmin', 'guard_name' => 'web']
        );

        $agent = Role::firstOrCreate(
            ['name' => 'agent', 'guard_name' => 'web']
        );

        $agent->syncPermissions([
            'tickets.view',
        ]);

        // 4. Superadmin: either give "all" permissions or let Gate::before handle it.
        // Here we still sync all, it's convenient for UI:
        $superadmin->syncPermissions(Permission::all());

        // 5. (Optional) assign superadmin to your own user for now
        $user = User::first(); // or find by email
        if ($user && !$user->hasRole('superadmin')) {
            $user->assignRole('superadmin');
        }
    }
}
