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
use App\Models\Document;
use App\Models\Tenant;
use App\Models\User;
use App\Services\NumHub\ApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\Mocks\NumHubMock;

/**
 * Feature tests for NumHub application submission flow.
 *
 * Tests the complete lifecycle of a BCID application:
 * 1. Creating draft application
 * 2. Uploading required documents
 * 3. OTP verification
 * 4. Submitting for review
 * 5. Handling approval/rejection
 *
 * @see \App\Services\NumHub\ApplicationService
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

    // Set up storage
    Storage::fake('local');

    // Configure NumHub
    config([
        'numhub.api_url' => 'https://brandidentity-api.numhub.com',
        'numhub.email' => 'test@brandcall.io',
        'numhub.password' => 'test_password',
        'numhub.client_id' => 'test_client_id',
    ]);
});

// =============================================================================
// APPLICATION CREATION
// =============================================================================

describe('application creation', function () {
    it('creates draft application from business model', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application' => NumHubMock::applicationCreated(),
        ]);

        $business = Business::factory()->create([
            'tenant_id' => $this->tenant->id,
            'legal_name' => 'Test Insurance Company LLC',
            'ein' => '12-3456789',
        ]);

        $service = app(ApplicationService::class);
        $result = $service->create($business);

        expect($result)->toHaveKey('NumhubEntityId');
        expect($result['status'])->toBe(1); // Saved (draft)

        // Entity ID should be stored on business
        $business->refresh();
        expect($business->numhub_entity_id)->toBe($result['NumhubEntityId']);
    })->skip('Implement Business model and ApplicationService first');

    it('maps business fields correctly to NumHub payload', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application' => NumHubMock::applicationCreated(),
        ]);

        $business = Business::factory()->create([
            'tenant_id' => $this->tenant->id,
            'legal_name' => 'Test Company LLC',
            'dba' => 'Test Co',
            'ein' => '12-3456789',
            'address_line1' => '123 Main St',
            'city' => 'Las Vegas',
            'state' => 'NV',
            'zip' => '89101',
            'phone' => '+17025551234',
            'website' => 'https://testcompany.com',
            'industry' => 'insurance',
        ]);

        $service = app(ApplicationService::class);
        $service->create($business);

        Http::assertSent(function ($request) {
            $body = $request->data();

            return $body['companyInfo']['legalName'] === 'Test Company LLC'
                && $body['companyInfo']['dba'] === 'Test Co'
                && $body['companyInfo']['ein'] === '12-3456789'
                && $body['companyInfo']['address']['city'] === 'Las Vegas'
                && $body['companyInfo']['address']['state'] === 'NV';
        });
    })->skip('Implement Business model and ApplicationService first');

    it('stores NumhubEntityId mapping', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application' => NumHubMock::applicationCreated(),
        ]);

        $business = Business::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $service = app(ApplicationService::class);
        $result = $service->create($business);

        // Check numhub_entities table
        $this->assertDatabaseHas('numhub_entities', [
            'numhub_entity_id' => $result['NumhubEntityId'],
            'business_id' => $business->id,
            'status' => 'saved',
        ]);
    })->skip('Implement NumHubEntity model and ApplicationService first');
});

// =============================================================================
// DOCUMENT UPLOAD
// =============================================================================

describe('document upload', function () {
    it('uploads business license to application', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456/documents' => [
                'success' => true,
                'documentId' => 'doc_001',
            ],
        ]);

        $business = Business::factory()->create([
            'tenant_id' => $this->tenant->id,
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $file = UploadedFile::fake()->create('license.pdf', 500, 'application/pdf');

        $service = app(ApplicationService::class);
        $result = $service->uploadDocument($business, 'business_license', $file);

        expect($result['success'])->toBeTrue();
        expect($result['documentId'])->toBe('doc_001');
    })->skip('Implement ApplicationService document upload first');

    it('uploads LOA document', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456/documents' => [
                'success' => true,
                'documentId' => 'doc_002',
            ],
        ]);

        $business = Business::factory()->create([
            'tenant_id' => $this->tenant->id,
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $file = UploadedFile::fake()->create('loa.pdf', 300, 'application/pdf');

        $service = app(ApplicationService::class);
        $result = $service->uploadDocument($business, 'loa', $file);

        expect($result['success'])->toBeTrue();
    })->skip('Implement ApplicationService document upload first');

    it('downloads LOA template', function () {
        $templateContent = 'PDF template content...';

        Http::fake([
            '*downloadtemplate*' => Http::response($templateContent, 200, [
                'Content-Type' => 'application/pdf',
            ]),
        ]);
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(ApplicationService::class);
        $template = $service->downloadLoaTemplate($business);

        expect($template)->toBe($templateContent);
    })->skip('Implement ApplicationService template download first');
});

// =============================================================================
// OTP VERIFICATION
// =============================================================================

describe('OTP verification', function () {
    it('generates OTP for verification', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456/generateOtp' => [
                'success' => true,
                'message' => 'OTP sent to registered email',
                'expiresIn' => 300,
            ],
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(ApplicationService::class);
        $result = $service->generateOtp($business);

        expect($result['success'])->toBeTrue();
    })->skip('Implement ApplicationService OTP first');

    it('verifies OTP code', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456/verifyOtp' => [
                'success' => true,
                'verified' => true,
            ],
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(ApplicationService::class);
        $result = $service->verifyOtp($business, '123456');

        expect($result['verified'])->toBeTrue();
    })->skip('Implement ApplicationService OTP first');

    it('fails with invalid OTP', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456/verifyOtp' => [
                'success' => false,
                'verified' => false,
                'error' => 'Invalid OTP code',
                '_status' => 400,
            ],
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(ApplicationService::class);
        $result = $service->verifyOtp($business, '000000');

        expect($result['verified'])->toBeFalse();
    })->skip('Implement ApplicationService OTP first');
});

// =============================================================================
// APPLICATION SUBMISSION
// =============================================================================

describe('application submission', function () {
    it('submits application for review', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456' => NumHubMock::application([
                'status' => 2,
                'statusDescription' => 'Submitted',
            ]),
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(ApplicationService::class);
        $result = $service->submit($business);

        expect($result['status'])->toBe(2); // Submitted

        // Check status updated in local database
        $this->assertDatabaseHas('numhub_entities', [
            'numhub_entity_id' => 'ent_abc123def456',
            'status' => 'submitted',
        ]);
    })->skip('Implement ApplicationService submit first');

    it('cannot submit without required documents', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456' => NumHubMock::validationError([
                [
                    'field' => 'documents',
                    'code' => 'REQUIRED',
                    'message' => 'Business license document is required',
                ],
            ]),
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(ApplicationService::class);
        $service->submit($business);
    })->throws(\App\Exceptions\NumHub\NumHubValidationException::class)
        ->skip('Implement ApplicationService submit first');

    it('cannot submit without OTP verification', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456' => NumHubMock::validationError([
                [
                    'field' => 'otpVerified',
                    'code' => 'REQUIRED',
                    'message' => 'OTP verification is required before submission',
                ],
            ]),
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(ApplicationService::class);
        $service->submit($business);
    })->throws(\App\Exceptions\NumHub\NumHubValidationException::class)
        ->skip('Implement ApplicationService submit first');
});

// =============================================================================
// STATUS CHECKING
// =============================================================================

describe('status checking', function () {
    it('retrieves current application status', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456' => NumHubMock::applicationApproved(),
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(ApplicationService::class);
        $status = $service->getStatus($business);

        expect($status['status'])->toBe(5); // Approved
        expect($status['statusDescription'])->toBe('Approved');
    })->skip('Implement ApplicationService getStatus first');

    it('retrieves vetting report', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/vetting-report/ent_abc123def456' => [
                'NumhubEntityId' => 'ent_abc123def456',
                'vettingStatus' => [
                    'score' => 95,
                    'level' => 'A',
                ],
            ],
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(ApplicationService::class);
        $report = $service->getVettingReport($business);

        expect($report['vettingStatus']['score'])->toBe(95);
        expect($report['vettingStatus']['level'])->toBe('A');
    })->skip('Implement ApplicationService getVettingReport first');
});

// =============================================================================
// APPROVAL HANDLING
// =============================================================================

describe('approval handling', function () {
    it('handles application approval', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456' => NumHubMock::applicationApproved(),
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(ApplicationService::class);
        $service->syncStatus($business);

        $business->refresh();
        expect($business->numhub_status)->toBe('approved');
        expect($business->numhub_approved_at)->not->toBeNull();

        // Check local entity record
        $this->assertDatabaseHas('numhub_entities', [
            'numhub_entity_id' => 'ent_abc123def456',
            'status' => 'approved',
        ]);
    })->skip('Implement ApplicationService syncStatus first');

    it('creates identities on approval', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456' => NumHubMock::applicationApproved(),
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(ApplicationService::class);
        $service->syncStatus($business);

        // Identities should be created locally
        $this->assertDatabaseHas('numhub_identities', [
            'numhub_entity_id' => 'ent_abc123def456',
        ]);
    })->skip('Implement identity sync first');
});

// =============================================================================
// REJECTION HANDLING
// =============================================================================

describe('rejection handling', function () {
    it('handles application rejection', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456' => NumHubMock::applicationRejected(),
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(ApplicationService::class);
        $service->syncStatus($business);

        $business->refresh();
        expect($business->numhub_status)->toBe('rejected');
    })->skip('Implement ApplicationService syncStatus first');

    it('stores rejection reasons', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456' => NumHubMock::applicationRejected(),
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
        ]);

        $service = app(ApplicationService::class);
        $service->syncStatus($business);

        $this->assertDatabaseHas('numhub_entities', [
            'numhub_entity_id' => 'ent_abc123def456',
            'status' => 'rejected',
        ]);

        $entity = \App\Models\NumHubEntity::where('numhub_entity_id', 'ent_abc123def456')->first();
        expect($entity->rejection_reasons)->not->toBeNull();
        expect($entity->rejection_reasons)->toBeArray();
    })->skip('Implement NumHubEntity model first');

    it('allows resubmission after rejection', function () {
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application/ent_abc123def456' => NumHubMock::application([
                'status' => 2,
                'statusDescription' => 'Submitted',
            ]),
        ]);

        $business = Business::factory()->create([
            'numhub_entity_id' => 'ent_abc123def456',
            'numhub_status' => 'rejected',
        ]);

        $service = app(ApplicationService::class);
        $result = $service->submit($business);

        expect($result['status'])->toBe(2);
    })->skip('Implement ApplicationService resubmit first');
});

// =============================================================================
// COMPLETE FLOW
// =============================================================================

describe('complete application flow', function () {
    it('completes full application lifecycle', function () {
        // Step 1: Create application
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            '/api/v1/application' => NumHubMock::applicationCreated(),
        ]);

        $business = Business::factory()->create([
            'tenant_id' => $this->tenant->id,
            'legal_name' => 'Complete Flow Test LLC',
        ]);

        $service = app(ApplicationService::class);
        $created = $service->create($business);
        $entityId = $created['NumhubEntityId'];

        // Step 2: Upload documents
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            "/api/v1/application/{$entityId}/documents" => ['success' => true, 'documentId' => 'doc_001'],
        ]);

        $file = UploadedFile::fake()->create('license.pdf', 500, 'application/pdf');
        $service->uploadDocument($business, 'business_license', $file);

        // Step 3: OTP verification
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            "/api/v1/application/{$entityId}/generateOtp" => ['success' => true],
            "/api/v1/application/{$entityId}/verifyOtp" => ['success' => true, 'verified' => true],
        ]);

        $service->generateOtp($business);
        $service->verifyOtp($business, '123456');

        // Step 4: Submit
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            "/api/v1/application/{$entityId}" => NumHubMock::application([
                'status' => 2,
                'statusDescription' => 'Submitted',
            ]),
        ]);

        $submitted = $service->submit($business);
        expect($submitted['status'])->toBe(2);

        // Step 5: Check approval
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
            "/api/v1/application/{$entityId}" => NumHubMock::applicationApproved(),
        ]);

        $service->syncStatus($business);

        $business->refresh();
        expect($business->numhub_status)->toBe('approved');
    })->skip('Implement full ApplicationService first');
});

// =============================================================================
// ERROR RECOVERY
// =============================================================================

describe('error recovery', function () {
    it('retries on rate limit', function () {
        NumHubMock::fakeRateLimit('/api/v1/application');
        NumHubMock::fake([
            '/api/v1/authorize/token' => NumHubMock::token(),
        ]);

        $business = Business::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $service = app(ApplicationService::class);
        $result = $service->create($business);

        expect($result)->toHaveKey('NumhubEntityId');
    })->skip('Implement rate limit retry in ApplicationService first');

    it('refreshes token on 401 and retries', function () {
        NumHubMock::fakeTokenRefreshFlow();

        $business = Business::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $service = app(ApplicationService::class);
        $result = $service->create($business);

        expect($result)->toHaveKey('NumhubEntityId');
    })->skip('Implement token refresh in NumHubClient first');
});
