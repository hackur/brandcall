<?php

/**
 * BrandCall - Branded Caller ID SaaS Platform.
 *
 * @author     BrandCall Development Team
 * @copyright  2024-2026 BrandCall
 * @license    Proprietary
 */

declare(strict_types=1);

use App\Models\Brand;
use App\Models\Business;
use App\Models\Tenant;
use App\Models\User;
use App\Services\NumHub\IdentityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\Mocks\NumHubMock;

/**
 * Feature tests for NumHub Display Identity management.
 *
 * Tests the management of caller ID display identities:
 * - Listing identities
 * - Creating/updating display info
 * - Managing phone numbers
 * - Deactivation requests
 *
 * @see \App\Services\NumHub\IdentityService
 */
uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed required roles
    Role::findOrCreate('owner');
    Role::findOrCreate('member');

    // Create tenant and user
    $this->tenant = Tenant::factory()->create();
    $this->user = User::factory()->create([
        'tenant_id' => $this->tenant->id,
        'status' => 'approved',
    ]);
    $this->user->assignRole('owner');

    // Configure NumHub
    config([
        'numhub.api_url' => 'https://brandidentity-api.numhub.com',
        'numhub.email' => 'test@brandcall.io',
        'numhub.password' => 'test_password',
        'numhub.client_id' => 'test_client_id',
    ]);
});

// =============================================================================
// LISTING IDENTITIES
// =============================================================================

describe('listing identities', function () {
    it('lists all identities for an entity', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/newdisplayidentity*' => NumHubMock::identityList(),
        ]);

        $business = Business::factory()->create([
            'tenant_id' => $this->tenant->id,
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(IdentityService::class);
        $result = $service->list($business);

        expect($result['data'])->toBeArray();
        expect($result['data'])->toHaveCount(3);
        expect($result['pagination'])->toHaveKey('totalItems');
    })->skip('Implement IdentityService first');

    it('filters identities by status', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/newdisplayidentity*' => [
                'data' => [
                    NumHubMock::identity(['status' => 'active']),
                ],
                'pagination' => ['totalItems' => 1],
            ],
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(IdentityService::class);
        $result = $service->list($business, ['status' => 'active']);

        expect($result['data'])->toHaveCount(1);
        expect($result['data'][0]['status'])->toBe('active');
    })->skip('Implement IdentityService first');

    it('paginates identity results', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/newdisplayidentity*' => [
                'data' => array_fill(0, 25, NumHubMock::identity()),
                'pagination' => [
                    'currentPage' => 1,
                    'perPage' => 25,
                    'totalItems' => 50,
                    'totalPages' => 2,
                    'hasNextPage' => true,
                ],
            ],
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(IdentityService::class);
        $result = $service->list($business, ['page' => 1, 'perPage' => 25]);

        expect($result['pagination']['hasNextPage'])->toBeTrue();
        expect($result['pagination']['totalPages'])->toBe(2);
    })->skip('Implement IdentityService first');
});

// =============================================================================
// GET SINGLE IDENTITY
// =============================================================================

describe('getting single identity', function () {
    it('retrieves identity by ID', function () {
        $identityId = 'idt_001abc';

        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            "/api/v1/applications/newidentities/{$identityId}" => NumHubMock::identity([
                'NumhubIdentityId' => $identityId,
                'displayName' => 'Test Insurance Co',
            ]),
        ]);

        $service = app(IdentityService::class);
        $identity = $service->get($identityId);

        expect($identity['NumhubIdentityId'])->toBe($identityId);
        expect($identity['displayName'])->toBe('Test Insurance Co');
    })->skip('Implement IdentityService first');

    it('returns null for non-existent identity', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/newidentities/*' => Http::response([
                'error' => 'Not found',
            ], 404),
        ]);

        $service = app(IdentityService::class);
        $identity = $service->get('idt_nonexistent');

        expect($identity)->toBeNull();
    })->skip('Implement IdentityService first');
});

// =============================================================================
// UPDATING IDENTITIES
// =============================================================================

describe('updating identities', function () {
    it('updates display name', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/updatedisplayidentity' => NumHubMock::identity([
                'displayName' => 'Updated Company Name',
            ]),
        ]);

        $service = app(IdentityService::class);
        $result = $service->update('idt_001abc', [
            'displayName' => 'Updated Company Name',
        ]);

        expect($result['displayName'])->toBe('Updated Company Name');

        Http::assertSent(function ($request) {
            return $request->method() === 'PUT'
                && $request['displayName'] === 'Updated Company Name';
        });
    })->skip('Implement IdentityService first');

    it('updates call reason', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/updatedisplayidentity' => NumHubMock::identity([
                'callReason' => 'Updated Call Reason',
            ]),
        ]);

        $service = app(IdentityService::class);
        $result = $service->update('idt_001abc', [
            'callReason' => 'Updated Call Reason',
        ]);

        expect($result['callReason'])->toBe('Updated Call Reason');
    })->skip('Implement IdentityService first');

    it('validates display name length', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/updatedisplayidentity' => NumHubMock::validationError([
                [
                    'field' => 'displayName',
                    'code' => 'MAX_LENGTH',
                    'message' => 'Display name must be 32 characters or less',
                ],
            ]),
        ]);

        $service = app(IdentityService::class);
        $service->update('idt_001abc', [
            'displayName' => 'This Display Name Is Way Too Long For CNAM',
        ]);
    })->throws(\App\Exceptions\NumHub\NumHubValidationException::class)
        ->skip('Implement IdentityService first');
});

// =============================================================================
// PHONE NUMBER MANAGEMENT
// =============================================================================

describe('phone number management', function () {
    it('bulk uploads phone numbers', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/idt_001abc/uploadadditionaltns' => [
                'success' => true,
                'added' => 10,
                'failed' => 0,
                'phoneNumbers' => [
                    '+17025551001',
                    '+17025551002',
                    '+17025551003',
                ],
            ],
        ]);

        $phoneNumbers = [
            '+17025551001',
            '+17025551002',
            '+17025551003',
        ];

        $service = app(IdentityService::class);
        $result = $service->uploadPhoneNumbers('idt_001abc', $phoneNumbers);

        expect($result['success'])->toBeTrue();
        expect($result['added'])->toBe(10);
    })->skip('Implement IdentityService first');

    it('handles partial upload failure', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/idt_001abc/uploadadditionaltns' => [
                'success' => true,
                'added' => 8,
                'failed' => 2,
                'errors' => [
                    [
                        'phoneNumber' => '+17025551009',
                        'reason' => 'Invalid phone number format',
                    ],
                    [
                        'phoneNumber' => '+17025551010',
                        'reason' => 'Number already assigned to another identity',
                    ],
                ],
            ],
        ]);

        $service = app(IdentityService::class);
        $result = $service->uploadPhoneNumbers('idt_001abc', ['+17025551001']);

        expect($result['failed'])->toBe(2);
        expect($result['errors'])->toHaveCount(2);
    })->skip('Implement IdentityService first');

    it('removes phone numbers from identity', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/idt_001abc/additionaltns' => [
                'success' => true,
                'removed' => 3,
            ],
        ]);

        $service = app(IdentityService::class);
        $result = $service->deletePhoneNumbers('idt_001abc', [
            '+17025551001',
            '+17025551002',
            '+17025551003',
        ]);

        expect($result['success'])->toBeTrue();
        expect($result['removed'])->toBe(3);
    })->skip('Implement IdentityService first');

    it('downloads phone number list as XLSX', function () {
        $xlsxContent = 'binary xlsx content...';

        Http::fake([
            '*downloadphonenumbers*' => Http::response($xlsxContent, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]),
        ]);
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
        ]);

        $service = app(IdentityService::class);
        $content = $service->downloadPhoneNumbers('idt_001abc');

        expect($content)->toBe($xlsxContent);
    })->skip('Implement IdentityService first');
});

// =============================================================================
// DEACTIVATION
// =============================================================================

describe('identity deactivation', function () {
    it('submits deactivation request', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/deactivationrequest' => [
                'success' => true,
                'NumhubIdentityId' => 'idt_001abc',
                'status' => 'deactivation_pending',
                'message' => 'Deactivation request submitted',
            ],
        ]);

        $service = app(IdentityService::class);
        $result = $service->deactivate('idt_001abc', 'No longer needed');

        expect($result['success'])->toBeTrue();
        expect($result['status'])->toBe('deactivation_pending');
    })->skip('Implement IdentityService first');

    it('requires reason for deactivation', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/deactivationrequest' => NumHubMock::validationError([
                [
                    'field' => 'reason',
                    'code' => 'REQUIRED',
                    'message' => 'Deactivation reason is required',
                ],
            ]),
        ]);

        $service = app(IdentityService::class);
        $service->deactivate('idt_001abc', '');
    })->throws(\App\Exceptions\NumHub\NumHubValidationException::class)
        ->skip('Implement IdentityService first');

    it('cannot deactivate already deactivated identity', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/deactivationrequest' => [
                'success' => false,
                'error' => 'Identity is already deactivated',
                '_status' => 400,
            ],
        ]);

        $service = app(IdentityService::class);
        $result = $service->deactivate('idt_001abc', 'Test reason');

        expect($result['success'])->toBeFalse();
    })->skip('Implement IdentityService first');
});

// =============================================================================
// LOCAL SYNC
// =============================================================================

describe('local identity sync', function () {
    it('syncs identities to local database', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/newdisplayidentity*' => NumHubMock::identityList(),
        ]);

        $business = Business::factory()->create([
            'tenant_id' => $this->tenant->id,
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(IdentityService::class);
        $service->sync($business);

        // Check local records were created
        $this->assertDatabaseHas('numhub_identities', [
            'numhub_entity_id' => 'ent_abc123def456',
            'numhub_identity_id' => 'idt_001abc',
        ]);
    })->skip('Implement identity sync first');

    it('updates existing local identities', function () {
        // Create existing local record
        \App\Models\NumHubIdentity::create([
            'numhub_identity_id' => 'idt_001abc',
            'numhub_entity_id' => 'ent_abc123def456',
            'display_name' => 'Old Name',
            'status' => 'pending',
        ]);

        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/newdisplayidentity*' => [
                'data' => [
                    NumHubMock::identity([
                        'NumhubIdentityId' => 'idt_001abc',
                        'displayName' => 'New Name',
                        'status' => 'active',
                    ]),
                ],
                'pagination' => ['totalItems' => 1],
            ],
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(IdentityService::class);
        $service->sync($business);

        $this->assertDatabaseHas('numhub_identities', [
            'numhub_identity_id' => 'idt_001abc',
            'display_name' => 'New Name',
            'status' => 'active',
        ]);
    })->skip('Implement identity sync first');

    it('marks removed identities as deactivated', function () {
        // Create local record that no longer exists on NumHub
        \App\Models\NumHubIdentity::create([
            'numhub_identity_id' => 'idt_removed',
            'numhub_entity_id' => 'ent_abc123def456',
            'status' => 'active',
        ]);

        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/newdisplayidentity*' => [
                'data' => [], // Empty - identity no longer exists
                'pagination' => ['totalItems' => 0],
            ],
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(IdentityService::class);
        $service->sync($business);

        $this->assertDatabaseHas('numhub_identities', [
            'numhub_identity_id' => 'idt_removed',
            'status' => 'deactivated',
        ]);
    })->skip('Implement identity sync first');
});

// =============================================================================
// BRAND INTEGRATION
// =============================================================================

describe('brand integration', function () {
    it('creates identity from brand model', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/newdisplayidentity' => NumHubMock::identity([
                'NumhubIdentityId' => 'idt_new123',
                'displayName' => 'Test Brand',
            ]),
        ]);

        $business = Business::factory()->create([
            'tenant_id' => $this->tenant->id,
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $brand = Brand::factory()->create([
            'tenant_id' => $this->tenant->id,
            'business_id' => $business->id,
            'display_name' => 'Test Brand',
            'call_reason' => 'Customer Service',
        ]);

        $service = app(IdentityService::class);
        $result = $service->createFromBrand($brand);

        expect($result['NumhubIdentityId'])->toBe('idt_new123');

        $brand->refresh();
        expect($brand->numhub_identity_id)->toBe('idt_new123');
    })->skip('Implement brand-identity integration first');

    it('syncs brand changes to identity', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/applications/updatedisplayidentity' => NumHubMock::identity([
                'displayName' => 'Updated Brand Name',
            ]),
        ]);

        $brand = Brand::factory()->create([
            'tenant_id' => $this->tenant->id,
            'numhub_identity_id' => 'idt_001abc',
            'display_name' => 'Updated Brand Name',
        ]);

        $service = app(IdentityService::class);
        $result = $service->syncFromBrand($brand);

        Http::assertSent(function ($request) {
            return $request['displayName'] === 'Updated Brand Name';
        });
    })->skip('Implement brand-identity sync first');
});
