<?php

/**
 * BrandCall - Branded Caller ID SaaS Platform.
 *
 * @author     BrandCall Development Team
 * @copyright  2024-2026 BrandCall
 * @license    Proprietary
 */

declare(strict_types=1);

namespace App\Services\NumHub\Contracts;

use App\Services\NumHub\DTOs\AccessToken;
use App\Services\NumHub\DTOs\Application;
use App\Services\NumHub\DTOs\BrandControlDeals;
use App\Services\NumHub\DTOs\CreateEntityResponse;
use App\Services\NumHub\DTOs\Deal;
use App\Services\NumHub\DTOs\DisplayIdentityResponse;
use App\Services\NumHub\DTOs\Note;
use App\Services\NumHub\DTOs\OspDefaultFee;
use App\Services\NumHub\DTOs\PaginatedResult;
use App\Services\NumHub\DTOs\SaveBCApplicationModel;
use App\Services\NumHub\DTOs\UpdateBCApplicationModel;
use App\Services\NumHub\DTOs\UpdateDisplayIdentityRequest;
use App\Services\NumHub\DTOs\UploadDocumentResponse;
use App\Services\NumHub\DTOs\UserInfo;
use App\Services\NumHub\Enums\DocumentType;
use Illuminate\Http\UploadedFile;

/**
 * NumHub BrandControl API Client Interface.
 *
 * Contract for NumHub API client implementations.
 * Enables dependency injection and testing with mock implementations.
 */
interface NumHubClientInterface
{
    // Authentication
    public function authenticate(): AccessToken;

    public function getUserInfo(): UserInfo;

    public function invalidateToken(): void;

    // Application Management
    public function createApplication(SaveBCApplicationModel $application): CreateEntityResponse;

    public function updateApplication(string $entityId, UpdateBCApplicationModel $application): CreateEntityResponse;

    public function getApplication(string $entityId): Application;

    public function listApplications(array $filters = []): PaginatedResult;

    // Documents
    public function uploadDocument(string $entityId, UploadedFile $file, DocumentType $type, ?string $description = null): UploadDocumentResponse;

    public function deleteDocument(string $entityId, string $documentId): bool;

    public function downloadLoaTemplate(string $entityId): string;

    // OTP
    public function generateOtp(string $entityId, bool $isResend = false): bool;

    public function verifyOtp(string $entityId, int $otp): bool;

    // Display Identities
    public function listDisplayIdentities(array $filters = []): array;

    public function getDisplayIdentity(string $identityId): DisplayIdentityResponse;

    public function updateDisplayIdentity(UpdateDisplayIdentityRequest $request): bool;

    public function deactivateDisplayIdentity(string $identityId, string $dirId): bool;

    // Deals
    public function listDeals(array $filters): PaginatedResult;

    public function createDeal(BrandControlDeals $deal): int;

    public function getDeal(int $dealId): Deal;

    public function updateDeal(int $dealId, BrandControlDeals $deal): bool;

    // Fees
    public function getDefaultFees(string $ospId): OspDefaultFee;

    public function createDefaultFees(string $ospId, OspDefaultFee $fees): bool;

    public function updateDefaultFees(string $ospId, OspDefaultFee $fees): bool;

    // Reports
    public function getSettlementReports(array $filters = []): PaginatedResult;

    // Utilities
    public function isConfigured(): bool;

    public function isMockMode(): bool;

    public function getRateLimitRemaining(): int;
}
