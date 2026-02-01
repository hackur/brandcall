<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * SmokeTest - Visits all pages and checks for errors.
 *
 * Run with: php artisan test --filter=SmokeTest
 */
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $owner;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\PricingTierSeeder::class);

        // Create tenant
        $this->tenant = Tenant::create([
            'name' => 'Test Company',
            'email' => 'test@example.com',
            'slug' => 'test-company',
            'subscription_tier' => 'starter',
        ]);

        // Create admin (no tenant)
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'tenant_id' => null,
        ]);
        $this->admin->assignRole('super-admin');

        // Create owner (with tenant)
        $this->owner = User::create([
            'name' => 'Owner User',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
            'tenant_id' => $this->tenant->id,
        ]);
        $this->owner->assignRole('owner');
    }

    // ==========================================
    // PUBLIC PAGES
    // ==========================================

    public function test_homepage_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_register_page_loads(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    // ==========================================
    // CUSTOMER DASHBOARD (Owner User)
    // ==========================================

    public function test_dashboard_loads_for_owner(): void
    {
        $response = $this->actingAs($this->owner)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_brands_index_loads(): void
    {
        $response = $this->actingAs($this->owner)->get('/brands');
        $response->assertStatus(200);
    }

    public function test_brands_create_loads(): void
    {
        $response = $this->actingAs($this->owner)->get('/brands/create');
        $response->assertStatus(200);
    }

    public function test_profile_page_loads(): void
    {
        $response = $this->actingAs($this->owner)->get('/profile');
        $response->assertStatus(200);
    }

    // ==========================================
    // ADMIN PANEL (Super Admin)
    // ==========================================

    public function test_admin_dashboard_loads(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_admin_tenants_loads(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/tenants');
        $response->assertStatus(200);
    }

    public function test_admin_brands_loads(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/brands');
        $response->assertStatus(200);
    }

    public function test_admin_users_loads(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_admin_tenant_create_loads(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/tenants/create');
        $response->assertStatus(200);
    }

    public function test_admin_brand_create_loads(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/brands/create');
        $response->assertStatus(200);
    }

    public function test_admin_user_create_loads(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users/create');
        $response->assertStatus(200);
    }

    // ==========================================
    // ADMIN PANEL - VIEW RECORDS
    // ==========================================

    public function test_admin_tenant_view_loads(): void
    {
        $response = $this->actingAs($this->admin)->get("/admin/tenants/{$this->tenant->id}");
        $response->assertStatus(200);
    }

    public function test_admin_tenant_edit_loads(): void
    {
        $response = $this->actingAs($this->admin)->get("/admin/tenants/{$this->tenant->id}/edit");
        $response->assertStatus(200);
    }

    // ==========================================
    // REDIRECTS & AUTH
    // ==========================================

    public function test_dashboard_redirects_guest_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_redirects_guest_to_admin_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_dashboard_redirects_super_admin(): void
    {
        // Super admin without tenant hitting /dashboard should redirect to /admin
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertRedirect('/admin');
    }

    public function test_onboarding_shown_for_user_without_tenant(): void
    {
        $userNoTenant = User::create([
            'name' => 'No Tenant User',
            'email' => 'notenant@test.com',
            'password' => bcrypt('password'),
            'tenant_id' => null,
        ]);
        $userNoTenant->assignRole('member');

        $response = $this->actingAs($userNoTenant)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Onboarding'));
    }
}
