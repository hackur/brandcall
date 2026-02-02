<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Onboarding Flow Tests.
 *
 * Tests the complete user onboarding journey from registration
 * through KYC document submission to approval.
 */
class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required roles
        Role::findOrCreate('member');
        Role::findOrCreate('owner');
        Role::findOrCreate('super-admin');
    }

    /**
     * Test that new users are redirected to onboarding.
     */
    public function test_new_user_sees_onboarding(): void
    {
        $user = User::factory()->create([
            'tenant_id' => null,
            'status' => 'pending',
        ]);
        $user->assignRole('member');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('onboarding.index'));
    }

    /**
     * Test that approved users with tenants go to dashboard.
     */
    public function test_approved_user_goes_to_dashboard(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'approved',
        ]);
        $user->assignRole('owner');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }

    /**
     * Test that super-admins are redirected to admin panel.
     */
    public function test_super_admin_redirected_to_admin(): void
    {
        $user = User::factory()->create([
            'tenant_id' => null,
            'status' => 'approved',
        ]);
        $user->assignRole('super-admin');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/admin');
    }

    /**
     * Test onboarding page loads for pending users.
     */
    public function test_onboarding_page_loads(): void
    {
        $user = User::factory()->create([
            'tenant_id' => null,
            'status' => 'pending',
        ]);
        $user->assignRole('member');

        $response = $this->actingAs($user)->get('/onboarding');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Onboarding/Dashboard'));
    }

    /**
     * Test onboarding profile page loads.
     */
    public function test_onboarding_profile_page_loads(): void
    {
        $user = User::factory()->create([
            'tenant_id' => null,
            'status' => 'pending',
        ]);
        $user->assignRole('member');

        $response = $this->actingAs($user)->get('/onboarding/profile');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Onboarding/Profile'));
    }

    /**
     * Test user can update profile.
     */
    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'tenant_id' => null,
            'status' => 'pending',
        ]);
        $user->assignRole('member');

        $response = $this->actingAs($user)->post('/onboarding/profile', [
            'company_name' => 'Test Insurance Co',
            'company_website' => 'https://testinsurance.com',
            'company_phone' => '555-123-4567',
            'company_address' => '123 Main St',
            'company_city' => 'Las Vegas',
            'company_state' => 'NV',
            'company_zip' => '89101',
            'industry' => 'insurance',
            'monthly_call_volume' => '1000-5000',
            'use_case' => 'Customer outreach',
        ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertEquals('Test Insurance Co', $user->company_name);
        $this->assertEquals('NV', $user->company_state);
    }
}
