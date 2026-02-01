<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Tenant permissions
            'view tenants',
            'create tenants',
            'edit tenants',
            'delete tenants',

            // Brand permissions
            'view brands',
            'create brands',
            'edit brands',
            'delete brands',
            'submit brands for vetting',

            // Phone number permissions
            'view phone numbers',
            'create phone numbers',
            'delete phone numbers',

            // Call permissions
            'view call logs',
            'make calls',

            // User/Team permissions
            'view users',
            'invite users',
            'edit users',
            'delete users',

            // Billing permissions
            'view billing',
            'manage billing',

            // Admin permissions
            'access admin panel',
            'manage pricing tiers',
            'approve brands',
            'view all tenants',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super Admin - platform administrator
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Owner - tenant owner (full access to their tenant)
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $owner->givePermissionTo([
            'view brands', 'create brands', 'edit brands', 'delete brands', 'submit brands for vetting',
            'view phone numbers', 'create phone numbers', 'delete phone numbers',
            'view call logs', 'make calls',
            'view users', 'invite users', 'edit users', 'delete users',
            'view billing', 'manage billing',
        ]);

        // Admin - tenant admin (most access, can't manage billing)
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'view brands', 'create brands', 'edit brands', 'submit brands for vetting',
            'view phone numbers', 'create phone numbers',
            'view call logs', 'make calls',
            'view users', 'invite users',
            'view billing',
        ]);

        // Member - regular team member
        $member = Role::firstOrCreate(['name' => 'member']);
        $member->givePermissionTo([
            'view brands',
            'view phone numbers',
            'view call logs', 'make calls',
        ]);

        $this->command->info('Roles and permissions seeded!');
    }
}
