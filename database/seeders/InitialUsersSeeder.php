<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin User (no tenant - platform admin)
        $admin = User::updateOrCreate(
            ['email' => 'admin@brandcall.io'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'tenant_id' => null,
            ]
        );
        $admin->assignRole('super-admin');

        $this->command->info('Super Admin created: admin@brandcall.io / password');

        // Create Demo Tenant
        $demoTenant = Tenant::updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo Insurance Company',
                'slug' => 'demo-insurance',
                'subscription_tier' => 'starter',
            ]
        );

        // Create Owner User
        $owner = User::updateOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'John Smith',
                'password' => Hash::make('password'),
                'tenant_id' => $demoTenant->id,
            ]
        );
        $owner->assignRole('owner');

        $this->command->info('Owner user created: owner@example.com / password');

        // Create Admin User (tenant admin)
        $tenantAdmin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Jane Doe',
                'password' => Hash::make('password'),
                'tenant_id' => $demoTenant->id,
            ]
        );
        $tenantAdmin->assignRole('admin');

        $this->command->info('Tenant Admin created: admin@example.com / password');

        // Create Member User
        $member = User::updateOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'Bob Wilson',
                'password' => Hash::make('password'),
                'tenant_id' => $demoTenant->id,
            ]
        );
        $member->assignRole('member');

        $this->command->info('Member user created: member@example.com / password');
        $this->command->info("Tenant: {$demoTenant->name}");

        // Create sample brands for demo tenant
        app()->instance('current_tenant', $demoTenant);

        Brand::updateOrCreate(
            ['slug' => 'health-insurance-florida'],
            [
                'tenant_id' => $demoTenant->id,
                'name' => 'Health Insurance Florida',
                'display_name' => 'FL Health Insurance',
                'call_reason' => 'Appointment Reminder',
                'status' => 'active',
                'api_key' => 'bci_' . bin2hex(random_bytes(32)),
            ]
        );

        Brand::updateOrCreate(
            ['slug' => 'health-insurance-california'],
            [
                'tenant_id' => $demoTenant->id,
                'name' => 'Health Insurance California',
                'display_name' => 'CA Health Insurance',
                'call_reason' => 'Policy Update',
                'status' => 'pending_vetting',
                'api_key' => 'bci_' . bin2hex(random_bytes(32)),
            ]
        );

        Brand::updateOrCreate(
            ['slug' => 'life-insurance-texas'],
            [
                'tenant_id' => $demoTenant->id,
                'name' => 'Life Insurance Texas',
                'display_name' => 'TX Life Insurance',
                'call_reason' => 'Claim Status',
                'status' => 'draft',
                'api_key' => 'bci_' . bin2hex(random_bytes(32)),
            ]
        );

        $this->command->info('Created 3 sample brands for demo tenant');
    }
}
