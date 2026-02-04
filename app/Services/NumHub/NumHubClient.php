<?php

/**
 * BrandCall - Branded Caller ID SaaS Platform.
 *
 * @author     BrandCall Development Team
 * @copyright  2024-2026 BrandCall
 * @license    Proprietary
 */

declare(strict_types=1);

namespace App\Services\NumHub;

use App\Services\NumHub\Contracts\NumHubClientInterface;
use App\Services\NumHub\DTOs\AccessToken;
use App\Services\NumHub\DTOs\Application;
use App\Services\NumHub\DTOs\AttestationEntity;
use App\Services\NumHub\DTOs\BrandControlDeals;
use App\Services\NumHub\DTOs\CreateEntityResponse;
use App\Services\NumHub\DTOs\Deal;
use App\Services\NumHub\DTOs\DisplayIdentity;
use App\Services\NumHub\DTOs\DisplayIdentityResponse;
use App\Services\NumHub\DTOs\Note;
use App\Services\NumHub\DTOs\Notification;
use App\Services\NumHub\DTOs\OspDefaultFee;
use App\Services\NumHub\DTOs\PaginatedResult;
use App\Services\NumHub\DTOs\SaveBCApplicationModel;
use App\Services\NumHub\DTOs\SettlementReport;
use App\Services\NumHub\DTOs\UpdateBCApplicationModel;
use App\Services\NumHub\DTOs\UpdateDisplayIdentityRequest;
use App\Services\NumHub\DTOs\UploadDocumentResponse;
use App\Services\NumHub\DTOs\UserInfo;
use App\Services\NumHub\Enums\DocumentType;
use App\Services\NumHub\Exceptions\ApiException;
use App\Services\NumHub\Exceptions\AuthenticationException;
use App\Services\NumHub\Exceptions\AuthorizationException;
use App\Services\NumHub\Exceptions\EntityNotFoundException;
use App\Services\NumHub\Exceptions\NumHubException;
use App\Services\NumHub\Exceptions\RateLimitException;
use App\Services\NumHub\Exceptions\ValidationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * NumHub BrandControl API Client.
 *
 * Service class for interacting with the NumHub BrandControl API.
 * Handles BCID application management, entity registration, display
 * identity management, and compliance workflows.
 *
 * This is SEPARATE from NumHubDriver (voice provider) - this client
 * handles the BrandControl administrative API, not voice calls.
 *
 * API Reference:
 * - Base URL: https://brandidentity-api.numhub.com
 * - Auth: Bearer token (24h expiry)
 * - Rate Limit: 100 requests/minute
 * - Auth Scheme: ATLAASROPG
 *
 * Usage:
 * ```php
 * $client = app(NumHubClient::class);
 *
 * // Authenticate (token auto-cached)
 * $token = $client->authenticate();
 *
 * // Create application
 * $response = $client->createApplication($dto);
 *
 * // List identities
 * $identities = $client->listDisplayIdentities(['pageNumber' => 1]);
 * ```
 *
 * @see https://brandidentity-api.numhub.com/docs/index.html
 * @see \App\Services\NumHub\DTOs For data transfer objects
 * @see \App\Services\NumHub\Exceptions For exception types
 */
class NumHubClient implements NumHubClientInterface
{
    /**
     * Cache key prefix for tokens.
     */
    private const TOKEN_CACHE_PREFIX = 'numhub:token:';

    /**
     * Cache key for rate limiting.
     */
    private const RATE_LIMIT_CACHE_KEY = 'numhub:rate_limit';

    /**
     * NumHub API configuration.
     *
     * @var array{
     *   api_url: string,
     *   email: string,
     *   password: string,
     *   client_id: ?int,
     *   auth_scheme: string,
     *   token_cache_ttl: int,
     *   timeout: int,
     *   mock: bool
     * }
     */
    private array $config;

    /**
     * Cached access token for current session.
     */
    private ?AccessToken $accessToken = null;

    /**
     * Create a new NumHub client instance.
     *
     * @param array $config NumHub configuration from config/numhub.php
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    // =========================================================================
    // AUTHENTICATION
    // =========================================================================

    /**
     * Authenticate with NumHub API and obtain access token.
     *
     * Tokens are valid for 24 hours. This method caches the token
     * with a 23.5 hour TTL to ensure refresh before expiry.
     *
     * The token response includes user info, roles, and available clients.
     * The first client's ID is automatically used for subsequent requests.
     *
     * @return AccessToken Access token with user info and expiry
     *
     * @throws AuthenticationException If credentials are invalid
     * @throws ApiException            If API request fails
     */
    public function authenticate(): AccessToken
    {
        // Check cache first
        $cacheKey = $this->getTokenCacheKey();
        $cached = Cache::get($cacheKey);

        if ($cached instanceof AccessToken && ! $cached->isExpired()) {
            $this->accessToken = $cached;

            return $cached;
        }

        // Request new token
        $response = Http::timeout($this->config['timeout'] ?? 30)
            ->asForm()
            ->post($this->baseUrl('/api/v1/authorize/token'), [
                'Email' => $this->config['email'],
                'Password' => $this->config['password'],
            ]);

        if (! $response->successful()) {
            throw new AuthenticationException(
                $response->json('message', 'Authentication failed')
            );
        }

        $data = $response->json();

        $token = new AccessToken(
            accessToken: $data['accessToken'],
            tokenType: $data['tokenType'],
            expiresIn: $data['expiresIn'],
            clientId: $data['userClients'][0]['clientId'] ?? $this->config['client_id'],
            user: $data['user'] ?? [],
            userRoles: $data['userRoles'] ?? [],
            userClients: $data['userClients'] ?? [],
            expiresAt: Carbon::now()->addSeconds($data['expiresIn']),
        );

        // Cache with buffer time (23.5 hours for 24h token)
        Cache::put(
            $cacheKey,
            $token,
            now()->addSeconds($this->config['token_cache_ttl'] ?? 84600)
        );

        $this->accessToken = $token;

        return $token;
    }

    /**
     * Get current user information including clients and roles.
     *
     * Requires prior authentication. Returns details about the
     * authenticated user, their assigned roles, and accessible clients.
     *
     * @return UserInfo User information with roles and clients
     *
     * @throws AuthenticationException If not authenticated
     * @throws ApiException            If API request fails
     */
    public function getUserInfo(): UserInfo
    {
        $response = $this->request('GET', '/api/v1/authorize/userInfo');

        return new UserInfo(
            userId: $response['user']['userId'],
            userName: $response['user']['userName'],
            email: $response['user']['email'],
            firstName: $response['user']['firstName'],
            lastName: $response['user']['lastName'],
            roles: $response['userRoles'] ?? [],
            clients: $response['userClients'] ?? [],
        );
    }

    /**
     * Invalidate cached token and force re-authentication.
     *
     * Use when token becomes invalid or credentials change.
     *
     * @return void
     */
    public function invalidateToken(): void
    {
        Cache::forget($this->getTokenCacheKey());
        $this->accessToken = null;
    }

    // =========================================================================
    // APPLICATION MANAGEMENT
    // =========================================================================

    /**
     * Create a new BCID application.
     *
     * Creates a new enterprise application in the NumHub system.
     * Set status to 1 to save as draft, or 2 to submit for processing.
     *
     * The returned EntityId is required for subsequent operations
     * (document upload, OTP verification, status updates).
     *
     * @param SaveBCApplicationModel $application Application data
     *
     * @return CreateEntityResponse Entity ID, application ID, and EID
     *
     * @throws ValidationException If application data is invalid
     * @throws RateLimitException  If rate limit exceeded
     * @throws ApiException        If API request fails
     */
    public function createApplication(SaveBCApplicationModel $application): CreateEntityResponse
    {
        $response = $this->request('POST', '/api/v1/application', $application->toArray());

        return new CreateEntityResponse(
            numhubEntityId: $response['numhubEntityId'],
            applicationId: $response['applicationId'] ?? null,
            eid: $response['eid'] ?? null,
        );
    }

    /**
     * Update an existing BCID application.
     *
     * Updates application details. Can be used to transition from
     * draft (status=1) to submitted (status=2).
     *
     * @param string                   $entityId    NumHub entity UUID
     * @param UpdateBCApplicationModel $application Updated application data
     *
     * @return CreateEntityResponse Updated entity response
     *
     * @throws EntityNotFoundException If entity not found
     * @throws ValidationException     If application data is invalid
     * @throws ApiException            If API request fails
     */
    public function updateApplication(string $entityId, UpdateBCApplicationModel $application): CreateEntityResponse
    {
        $response = $this->request('PUT', "/api/v1/application/{$entityId}", $application->toArray());

        return new CreateEntityResponse(
            numhubEntityId: $response['numhubEntityId'],
            applicationId: $response['applicationId'] ?? null,
            eid: $response['eid'] ?? null,
        );
    }

    /**
     * Get application details by NumHub entity ID.
     *
     * Retrieves full application details including business info,
     * display identities, documents, and current status.
     *
     * @param string $entityId NumHub entity UUID
     *
     * @return Application Full application details
     *
     * @throws EntityNotFoundException If entity not found
     * @throws ApiException            If API request fails
     */
    public function getApplication(string $entityId): Application
    {
        $response = $this->request('GET', "/api/v1/application/{$entityId}");

        return Application::fromArray($response['result'] ?? $response);
    }

    /**
     * Get application in view mode (read-only).
     *
     * Similar to getApplication but optimized for display purposes.
     *
     * @param string $entityId NumHub entity UUID
     *
     * @return Application Application details
     *
     * @throws EntityNotFoundException If entity not found
     * @throws ApiException            If API request fails
     */
    public function getApplicationView(string $entityId): Application
    {
        $response = $this->request('GET', "/api/v1/application/{$entityId}/view");

        return Application::fromArray($response['result'] ?? $response);
    }

    /**
     * Get application with vetting report.
     *
     * Includes detailed vetting/verification information along
     * with application details.
     *
     * @param string $entityId NumHub entity UUID
     *
     * @return Application Application with vetting details
     *
     * @throws EntityNotFoundException If entity not found
     * @throws ApiException            If API request fails
     */
    public function getApplicationWithVetting(string $entityId): Application
    {
        $response = $this->request('GET', "/api/v1/application/vetting-report/{$entityId}");

        return Application::fromArray($response['result'] ?? $response);
    }

    /**
     * List all applications with filtering and pagination.
     *
     * Supports filtering by status, date range, client ID, and search key.
     *
     * @param array{
     *   pageNumber?: int,
     *   pageSize?: int,
     *   status?: string,
     *   searchKey?: string,
     *   startDate?: \DateTimeInterface,
     *   endDate?: \DateTimeInterface,
     *   clientId?: int
     * } $filters Filter and pagination options
     *
     * @return PaginatedResult<Application> Paginated list of applications
     *
     * @throws ApiException If API request fails
     */
    public function listApplications(array $filters = []): PaginatedResult
    {
        $response = $this->request('GET', '/api/v1/application', $this->formatFilters($filters));

        return PaginatedResult::fromArray($response, Application::class);
    }

    /**
     * List completed applications for a client.
     *
     * Returns only applications with "Complete" status.
     *
     * @param int $clientId Client ID to filter by
     *
     * @return array<Application> List of completed applications
     *
     * @throws ApiException If API request fails
     */
    public function listCompletedApplications(int $clientId): array
    {
        $response = $this->request('GET', "/api/v1/application/{$clientId}/completedEntities");

        return array_map(
            fn (array $item) => Application::fromArray($item),
            $response['result'] ?? $response ?? []
        );
    }

    /**
     * Get application by Enterprise ID (EID).
     *
     * @param string $eid Enterprise ID assigned by BCID system
     *
     * @return Application Application details
     *
     * @throws EntityNotFoundException If EID not found
     * @throws ApiException            If API request fails
     */
    public function getApplicationByEid(string $eid): Application
    {
        $response = $this->request('GET', "/api/v1/application/{$eid}/enterprise");

        return Application::fromArray($response);
    }

    /**
     * List EIDs for an OSP (Originating Service Provider).
     *
     * Returns all Enterprise IDs associated with the given OSP.
     *
     * @param string $ospId OSP identifier
     *
     * @return array{totalCount: int, associatedEIds: array} EID list with status
     *
     * @throws ApiException If API request fails
     */
    public function listEidsForOsp(string $ospId): array
    {
        return $this->request('GET', "/api/v1/application/{$ospId}/eids");
    }

    // =========================================================================
    // APPLICATION DOCUMENTS
    // =========================================================================

    /**
     * Upload a document for an application.
     *
     * Supports three document types:
     * - LOA: Letter of Authorization (PDF only)
     * - LOGO: Brand logo (BMP only)
     * - DOCUMENTS: Supporting documents (PDF, XLSX, CSV)
     *
     * @param string            $entityId    NumHub entity UUID
     * @param UploadedFile      $file        File to upload
     * @param DocumentType      $type        Document type
     * @param string|null       $description Optional description
     *
     * @return UploadDocumentResponse Upload result with document ID
     *
     * @throws ValidationException     If file type invalid for document type
     * @throws EntityNotFoundException If entity not found
     * @throws ApiException            If API request fails
     */
    public function uploadDocument(
        string $entityId,
        UploadedFile $file,
        DocumentType $type,
        ?string $description = null
    ): UploadDocumentResponse {
        $response = $this->client()
            ->attach('Files', $file->getContent(), $file->getClientOriginalName())
            ->post($this->baseUrl("/api/v1/application/{$entityId}/documents"), [
                'DocumentType' => $type->value,
                'Description' => $description,
            ]);

        return new UploadDocumentResponse(
            success: $response->json('success', true),
            documentId: $response->json('documentId'),
            message: $response->json('message'),
        );
    }

    /**
     * Delete an uploaded document.
     *
     * @param string $entityId   NumHub entity UUID
     * @param string $documentId Document UUID to delete
     *
     * @return bool True if deleted successfully
     *
     * @throws EntityNotFoundException If entity or document not found
     * @throws ApiException            If API request fails
     */
    public function deleteDocument(string $entityId, string $documentId): bool
    {
        $response = $this->request('DELETE', "/api/v1/application/{$entityId}/documents/{$documentId}");

        return $response['success'] ?? true;
    }

    /**
     * Download LOA (Letter of Authorization) template.
     *
     * Returns the BCID Appendix A - TN Authorization template
     * that must be signed by the enterprise.
     *
     * @param string $entityId NumHub entity UUID
     *
     * @return string Binary PDF content
     *
     * @throws EntityNotFoundException If entity not found
     * @throws ApiException            If API request fails
     */
    public function downloadLoaTemplate(string $entityId): string
    {
        $response = $this->client()->get(
            $this->baseUrl("/api/v1/application/{$entityId}/downloadtemplate")
        );

        if (! $response->successful()) {
            $this->handleErrorResponse($response);
        }

        return $response->body();
    }

    // =========================================================================
    // OTP VERIFICATION
    // =========================================================================

    /**
     * Generate and send OTP to applicant.
     *
     * Sends a one-time password to the applicant's email for
     * identity verification (Step 1 of application process).
     * OTP is valid for 24 hours.
     *
     * @param string $entityId NumHub entity UUID
     * @param bool   $isResend True to resend OTP
     *
     * @return bool True if OTP sent successfully
     *
     * @throws EntityNotFoundException If entity not found
     * @throws ApiException            If API request fails
     */
    public function generateOtp(string $entityId, bool $isResend = false): bool
    {
        $response = $this->request('POST', "/api/v1/application/{$entityId}/generateOtp", [
            'isResend' => $isResend,
        ]);

        return $response['success'] ?? true;
    }

    /**
     * Verify OTP code entered by applicant.
     *
     * Validates the OTP and updates verification status.
     * OTP must have been generated within 24 hours.
     *
     * @param string $entityId NumHub entity UUID
     * @param int    $otp      OTP code entered by user
     *
     * @return bool True if OTP verified successfully
     *
     * @throws ValidationException     If OTP invalid or expired
     * @throws EntityNotFoundException If entity not found
     * @throws ApiException            If API request fails
     */
    public function verifyOtp(string $entityId, int $otp): bool
    {
        $response = $this->request('POST', "/api/v1/application/{$entityId}/verifyOtp", [
            'otp' => $otp,
        ]);

        return $response['success'] ?? true;
    }

    // =========================================================================
    // APPLICATION AUTHORIZATION (ADMIN)
    // =========================================================================

    /**
     * Approve or reject an application.
     *
     * Global admin only. Sets application status to Approved or Rejected
     * with an optional comment.
     *
     * @param string $entityId NumHub entity UUID
     * @param string $status   'Approved' or 'Rejected'
     * @param string $comment  Reason/notes for the decision
     *
     * @return array{success: bool, noteId: string} Result with note ID
     *
     * @throws AuthorizationException  If user lacks admin privileges
     * @throws EntityNotFoundException If entity not found
     * @throws ApiException            If API request fails
     */
    public function authorizeApplication(string $entityId, string $status, string $comment): array
    {
        return $this->request('POST', "/api/v1/application/{$entityId}/authorize", [
            'status' => $status,
            'comment' => $comment,
        ]);
    }

    /**
     * Get a specific note/comment on an application.
     *
     * Notes contain status change history with comments.
     *
     * @param string $entityId NumHub entity UUID
     * @param string $noteId   Note UUID
     *
     * @return Note Note with status and comments
     *
     * @throws EntityNotFoundException If entity or note not found
     * @throws ApiException            If API request fails
     */
    public function getNote(string $entityId, string $noteId): Note
    {
        $response = $this->request('GET', "/api/v1/application/{$entityId}/notes/{$noteId}");

        return Note::fromArray($response);
    }

    // =========================================================================
    // DISPLAY IDENTITY MANAGEMENT
    // =========================================================================

    /**
     * List caller display identities with filtering.
     *
     * Returns caller IDs (display names, logos, call reasons) that
     * can be displayed on outbound calls.
     *
     * Filter by Dir ID, CallerName, CompanyName, or PhoneNumber.
     *
     * @param array{
     *   pageNumber?: int,
     *   pageSize?: int,
     *   searchKey?: string,
     *   startDate?: \DateTimeInterface,
     *   endDate?: \DateTimeInterface
     * } $filters Filter and pagination options
     *
     * @return array<DisplayIdentityResponse> List of display identities
     *
     * @throws ApiException If API request fails
     */
    public function listDisplayIdentities(array $filters = []): array
    {
        $response = $this->request('GET', '/api/v1/applications/newdisplayidentity', $this->formatFilters($filters));

        return array_map(
            fn (array $item) => DisplayIdentityResponse::fromArray($item),
            $response ?? []
        );
    }

    /**
     * Get a specific display identity by ID.
     *
     * @param string $identityId NumHub identity UUID
     *
     * @return DisplayIdentityResponse Display identity details
     *
     * @throws EntityNotFoundException If identity not found
     * @throws ApiException            If API request fails
     */
    public function getDisplayIdentity(string $identityId): DisplayIdentityResponse
    {
        $response = $this->request('GET', "/api/v1/applications/newidentities/{$identityId}");

        return DisplayIdentityResponse::fromArray($response);
    }

    /**
     * Update a display identity.
     *
     * Update caller name, call reason, logo URL, or phone numbers
     * for an existing identity.
     *
     * @param UpdateDisplayIdentityRequest $request Updated identity data
     *
     * @return bool True if updated successfully
     *
     * @throws ValidationException     If data invalid
     * @throws EntityNotFoundException If identity not found
     * @throws ApiException            If API request fails
     */
    public function updateDisplayIdentity(UpdateDisplayIdentityRequest $request): bool
    {
        $response = $this->request('PUT', '/api/v1/applications/updatedisplayidentity', $request->toArray());

        return $response['success'] ?? true;
    }

    /**
     * Request deactivation of a display identity.
     *
     * Submits a deactivation request for the identity.
     *
     * @param string $identityId NumHub identity UUID
     * @param string $dirId      Directory ID
     *
     * @return bool True if deactivation requested
     *
     * @throws EntityNotFoundException If identity not found
     * @throws ApiException            If API request fails
     */
    public function deactivateDisplayIdentity(string $identityId, string $dirId): bool
    {
        $response = $this->request('PUT', '/api/v1/applications/deactivationrequest', [
            'numhubIdentityId' => $identityId,
            'dirId' => $dirId,
            'isDeactivationRequest' => true,
        ]);

        return $response['success'] ?? true;
    }

    /**
     * Bulk upload phone numbers for an identity.
     *
     * Adds additional telephone numbers (TNs) to an existing
     * display identity via file upload.
     *
     * @param string       $identityId NumHub identity UUID
     * @param UploadedFile $file       XLSX/CSV file with phone numbers
     *
     * @return bool True if upload successful
     *
     * @throws ValidationException     If file format invalid
     * @throws EntityNotFoundException If identity not found
     * @throws ApiException            If API request fails
     */
    public function uploadAdditionalPhoneNumbers(string $identityId, UploadedFile $file): bool
    {
        $response = $this->client()
            ->attach('file', $file->getContent(), $file->getClientOriginalName())
            ->post($this->baseUrl("/api/v1/applications/{$identityId}/uploadadditionaltns"));

        return $response->json('success', $response->successful());
    }

    /**
     * Remove phone numbers from an identity.
     *
     * @param string   $identityId   NumHub identity UUID
     * @param string[] $phoneNumbers Phone numbers to remove (E.164)
     *
     * @return bool True if removed successfully
     *
     * @throws EntityNotFoundException If identity not found
     * @throws ApiException            If API request fails
     */
    public function removePhoneNumbers(string $identityId, array $phoneNumbers): bool
    {
        $response = $this->request('DELETE', "/api/v1/applications/{$identityId}/additionaltns", [
            'phoneNumbers' => $phoneNumbers,
        ]);

        return $response['success'] ?? true;
    }

    /**
     * Download phone numbers as XLSX file.
     *
     * Exports all phone numbers for the authenticated client.
     *
     * @param array{
     *   identityId?: string,
     *   format?: string
     * } $filters Optional filters
     *
     * @return string Binary XLSX content
     *
     * @throws ApiException If API request fails
     */
    public function downloadPhoneNumbers(array $filters = []): string
    {
        $response = $this->client()->get(
            $this->baseUrl('/api/v1/applications/downloadphonenumbers'),
            $filters
        );

        if (! $response->successful()) {
            $this->handleErrorResponse($response);
        }

        return $response->body();
    }

    // =========================================================================
    // ATTESTATION (STIR/SHAKEN)
    // =========================================================================

    /**
     * Get attestation entities for a client.
     *
     * Lists entities that can submit STIR/SHAKEN attestation.
     *
     * @param int   $clientId   Client ID
     * @param array $filters    Pagination filters
     *
     * @return PaginatedResult<AttestationEntity> Paginated attestation entities
     *
     * @throws ApiException If API request fails
     */
    public function getAttestationEntities(int $clientId, array $filters = []): PaginatedResult
    {
        $response = $this->request('GET', "/api/v1/application/attestation/{$clientId}", $this->formatFilters($filters));

        return PaginatedResult::fromArray($response, AttestationEntity::class);
    }

    /**
     * Submit attestation for an entity.
     *
     * Submits STIR/SHAKEN attestation level (A, B, or C) for
     * the specified entity.
     *
     * @param string $entityId    NumHub entity UUID
     * @param array  $attestation Attestation data (level, details)
     *
     * @return bool True if attestation submitted
     *
     * @throws ValidationException     If attestation data invalid
     * @throws EntityNotFoundException If entity not found
     * @throws ApiException            If API request fails
     */
    public function submitAttestation(string $entityId, array $attestation): bool
    {
        $response = $this->request('PUT', "/api/v1/application/attestation/{$entityId}", $attestation);

        return $response['success'] ?? true;
    }

    // =========================================================================
    // DEALS MANAGEMENT (OSP/RESELLER)
    // =========================================================================

    /**
     * List deals with filtering and pagination.
     *
     * For OSP users to manage enterprise/BPO deals.
     *
     * @param array{
     *   pageNumber: int,
     *   pageSize: int,
     *   clientName?: string,
     *   customerEmailAddress?: string,
     *   startDate?: \DateTimeInterface,
     *   endDate?: \DateTimeInterface,
     *   sortBy?: string,
     *   isDescending?: bool
     * } $filters Filter and sort options
     *
     * @return PaginatedResult<Deal> Paginated list of deals
     *
     * @throws ApiException If API request fails
     */
    public function listDeals(array $filters): PaginatedResult
    {
        $response = $this->request('GET', '/api/v1/deals', $this->formatFilters($filters));

        return PaginatedResult::fromArray($response, Deal::class);
    }

    /**
     * Create a new deal.
     *
     * Creates Enterprise or BPO deal with client information
     * and fee structure.
     *
     * @param BrandControlDeals $deal Deal data
     *
     * @return int Created deal ID
     *
     * @throws ValidationException If deal data invalid
     * @throws ApiException        If API request fails
     */
    public function createDeal(BrandControlDeals $deal): int
    {
        $response = $this->request('POST', '/api/v1/deals', $deal->toArray());

        return $response['result']['dealId'] ?? $response['dealId'];
    }

    /**
     * Get deal by ID.
     *
     * @param int $dealId Deal ID
     *
     * @return Deal Deal details
     *
     * @throws EntityNotFoundException If deal not found
     * @throws ApiException            If API request fails
     */
    public function getDeal(int $dealId): Deal
    {
        $response = $this->request('GET', "/api/v1/deals/{$dealId}");

        return Deal::fromArray($response);
    }

    /**
     * Update an existing deal.
     *
     * @param int               $dealId Deal ID
     * @param BrandControlDeals $deal   Updated deal data
     *
     * @return bool True if updated successfully
     *
     * @throws EntityNotFoundException If deal not found
     * @throws ValidationException     If deal data invalid
     * @throws ApiException            If API request fails
     */
    public function updateDeal(int $dealId, BrandControlDeals $deal): bool
    {
        $response = $this->request('PUT', "/api/v1/deals/{$dealId}", $deal->toArray());

        return $response['success'] ?? true;
    }

    /**
     * Resend deal registration email.
     *
     * Resends the registration email to the customer.
     *
     * @param int $dealId Deal ID
     *
     * @return bool True if email sent
     *
     * @throws EntityNotFoundException If deal not found
     * @throws ApiException            If API request fails
     */
    public function resendDealEmail(int $dealId): bool
    {
        $response = $this->request('POST', "/api/v1/deals/{$dealId}/resendEmail");

        return $response['success'] ?? true;
    }

    // =========================================================================
    // FEE MANAGEMENT
    // =========================================================================

    /**
     * Get default fees for an OSP.
     *
     * @param string $ospId OSP identifier
     *
     * @return OspDefaultFee Fee structure
     *
     * @throws EntityNotFoundException If OSP not found
     * @throws ApiException            If API request fails
     */
    public function getDefaultFees(string $ospId): OspDefaultFee
    {
        $response = $this->request('GET', "/api/v1/deals/fees/{$ospId}");

        return OspDefaultFee::fromArray($response);
    }

    /**
     * Create default fee structure for an OSP.
     *
     * @param string        $ospId OSP identifier
     * @param OspDefaultFee $fees  Fee structure
     *
     * @return bool True if created successfully
     *
     * @throws ValidationException If fee data invalid
     * @throws ApiException        If API request fails
     */
    public function createDefaultFees(string $ospId, OspDefaultFee $fees): bool
    {
        $response = $this->request('POST', "/api/v1/deals/fees/{$ospId}", $fees->toArray());

        return $response['success'] ?? true;
    }

    /**
     * Update default fee structure for an OSP.
     *
     * @param string        $ospId OSP identifier
     * @param OspDefaultFee $fees  Updated fee structure
     *
     * @return bool True if updated successfully
     *
     * @throws EntityNotFoundException If OSP not found
     * @throws ValidationException     If fee data invalid
     * @throws ApiException            If API request fails
     */
    public function updateDefaultFees(string $ospId, OspDefaultFee $fees): bool
    {
        $response = $this->request('PUT', "/api/v1/deals/fees/{$ospId}", $fees->toArray());

        return $response['success'] ?? true;
    }

    // =========================================================================
    // REPORTS
    // =========================================================================

    /**
     * Get BCID settlement reports.
     *
     * Returns billing/usage data for BCID services.
     *
     * @param array{
     *   pageNumber?: int,
     *   pageSize?: int,
     *   startDate?: \DateTimeInterface,
     *   endDate?: \DateTimeInterface,
     *   clientIds?: string,
     *   tspName?: string,
     *   saId?: string,
     *   vaId?: string,
     *   bpoId?: string
     * } $filters Report filters
     *
     * @return PaginatedResult<SettlementReport> Paginated settlement reports
     *
     * @throws ApiException If API request fails
     */
    public function getSettlementReports(array $filters = []): PaginatedResult
    {
        $response = $this->request(
            'GET',
            '/api/v1/confirmationReports/settlementReports',
            $this->formatFilters($filters)
        );

        return PaginatedResult::fromArray($response, SettlementReport::class);
    }

    /**
     * Get application status report.
     *
     * Returns counts of applications by status.
     *
     * @param array $filters Optional date/client filters
     *
     * @return array Status counts
     *
     * @throws ApiException If API request fails
     */
    public function getStatusReport(array $filters = []): array
    {
        return $this->request('GET', '/api/v1/reports/status', $this->formatFilters($filters));
    }

    /**
     * Get deals report by period.
     *
     * @param array $filters Period and client filters
     *
     * @return array Deals data by period
     *
     * @throws ApiException If API request fails
     */
    public function getDealsReport(array $filters = []): array
    {
        return $this->request('GET', '/api/v1/reports/deals', $this->formatFilters($filters));
    }

    // =========================================================================
    // NOTIFICATIONS
    // =========================================================================

    /**
     * Get flagged application notifications/alerts.
     *
     * Returns applications that have been flagged for attention.
     *
     * @return array<Notification> List of notifications
     *
     * @throws ApiException If API request fails
     */
    public function getNotifications(): array
    {
        $response = $this->request('GET', '/api/v1/notifications');

        return array_map(
            fn (array $item) => Notification::fromArray($item),
            $response ?? []
        );
    }

    /**
     * Get specific flag details for an entity.
     *
     * @param string $entityId NumHub entity UUID
     *
     * @return array Flag details with comments
     *
     * @throws EntityNotFoundException If entity not found
     * @throws ApiException            If API request fails
     */
    public function getEntityFlags(string $entityId): array
    {
        return $this->request('GET', "/api/v1/notifications/{$entityId}/flag");
    }

    // =========================================================================
    // DOCUMENTS (Global)
    // =========================================================================

    /**
     * List all documents for authenticated client.
     *
     * @return array<array> List of documents
     *
     * @throws ApiException If API request fails
     */
    public function listDocuments(): array
    {
        return $this->request('GET', '/api/v1/Documents');
    }

    /**
     * Get document by ID.
     *
     * @param int $documentId Document ID
     *
     * @return array Document details
     *
     * @throws EntityNotFoundException If document not found
     * @throws ApiException            If API request fails
     */
    public function getDocument(int $documentId): array
    {
        return $this->request('GET', "/api/v1/Documents/{$documentId}");
    }

    // =========================================================================
    // ENTERPRISE/BCID LOOKUP
    // =========================================================================

    /**
     * Get child client IDs and names for OSP.
     *
     * Returns list of enterprises under the OSP account.
     *
     * @return array<array{clientId: int, clientName: string}> Enterprise list
     *
     * @throws ApiException If API request fails
     */
    public function getChildClients(): array
    {
        return $this->request('GET', '/api/v1/confirmationReports/getchildclientidandname');
    }

    // =========================================================================
    // UTILITY METHODS
    // =========================================================================

    /**
     * Check if the client is configured and ready to use.
     *
     * @return bool True if API credentials are configured
     */
    public function isConfigured(): bool
    {
        return ! empty($this->config['email'])
            && ! empty($this->config['password'])
            && ! $this->isMockMode();
    }

    /**
     * Check if running in mock mode (for testing).
     *
     * @return bool True if mock mode enabled
     */
    public function isMockMode(): bool
    {
        return $this->config['mock'] ?? false;
    }

    /**
     * Get remaining rate limit requests.
     *
     * @return int Requests remaining in current window
     */
    public function getRateLimitRemaining(): int
    {
        $maxRequests = $this->config['rate_limit']['max_requests'] ?? 100;
        $current = Cache::get(self::RATE_LIMIT_CACHE_KEY, 0);

        return max(0, $maxRequests - $current);
    }

    // =========================================================================
    // PROTECTED METHODS
    // =========================================================================

    /**
     * Make an authenticated API request.
     *
     * @param string $method   HTTP method
     * @param string $endpoint API endpoint
     * @param array  $data     Request data (query for GET, body for POST/PUT)
     *
     * @return array Response data
     *
     * @throws NumHubException On any API error
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $this->checkRateLimit();

        $response = match (strtoupper($method)) {
            'GET' => $this->client()->get($this->baseUrl($endpoint), $data),
            'POST' => $this->client()->post($this->baseUrl($endpoint), $data),
            'PUT' => $this->client()->put($this->baseUrl($endpoint), $data),
            'DELETE' => $this->client()->delete($this->baseUrl($endpoint), $data),
            default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
        };

        $this->trackRateLimit($response);

        if (! $response->successful()) {
            $this->handleErrorResponse($response);
        }

        return $response->json() ?? [];
    }

    /**
     * Get configured HTTP client with authentication headers.
     *
     * @return PendingRequest Configured HTTP client
     */
    protected function client(): PendingRequest
    {
        // Ensure we have a token
        if (! $this->accessToken) {
            $this->authenticate();
        }

        return Http::baseUrl($this->config['api_url'])
            ->withHeaders([
                'Authorization' => "Bearer {$this->accessToken->accessToken}",
                'client-id' => (string) $this->accessToken->clientId,
                'X-Auth-Scheme' => $this->config['auth_scheme'] ?? 'ATLAASROPG',
                'Accept' => 'application/json',
            ])
            ->timeout($this->config['timeout'] ?? 30)
            ->retry(
                $this->config['retry']['times'] ?? 3,
                $this->config['retry']['sleep'] ?? 1000,
                function (\Exception $e, PendingRequest $request) {
                    // Retry on server errors and rate limits
                    if ($e instanceof \Illuminate\Http\Client\RequestException) {
                        $status = $e->response->status();

                        return in_array($status, $this->config['retry']['when'] ?? [429, 500, 502, 503, 504]);
                    }

                    return false;
                }
            );
    }

    /**
     * Build full API URL.
     *
     * @param string $endpoint API endpoint path
     *
     * @return string Full URL
     */
    protected function baseUrl(string $endpoint): string
    {
        $base = rtrim($this->config['api_url'], '/');

        return $base . '/' . ltrim($endpoint, '/');
    }

    /**
     * Get cache key for token storage.
     *
     * @return string Cache key
     */
    protected function getTokenCacheKey(): string
    {
        return self::TOKEN_CACHE_PREFIX . md5($this->config['email'] ?? 'default');
    }

    /**
     * Check rate limit before making request.
     *
     * @throws RateLimitException If rate limit exceeded
     */
    protected function checkRateLimit(): void
    {
        $maxRequests = $this->config['rate_limit']['max_requests'] ?? 100;
        $windowSeconds = $this->config['rate_limit']['window_seconds'] ?? 60;
        $current = Cache::get(self::RATE_LIMIT_CACHE_KEY, 0);

        if ($current >= $maxRequests) {
            throw new RateLimitException(
                'NumHub rate limit exceeded. Try again later.',
                $windowSeconds
            );
        }

        Cache::put(
            self::RATE_LIMIT_CACHE_KEY,
            $current + 1,
            now()->addSeconds($windowSeconds)
        );
    }

    /**
     * Track rate limit from response headers.
     *
     * @param Response $response HTTP response
     */
    protected function trackRateLimit(Response $response): void
    {
        if ($remaining = $response->header('requests-remaining')) {
            // Update cache with actual remaining from API
            $maxRequests = $this->config['rate_limit']['max_requests'] ?? 100;
            $used = $maxRequests - (int) $remaining;
            Cache::put(self::RATE_LIMIT_CACHE_KEY, max(0, $used), now()->addMinute());
        }
    }

    /**
     * Handle error response and throw appropriate exception.
     *
     * @param Response $response HTTP response
     *
     * @throws NumHubException Appropriate exception for error type
     */
    protected function handleErrorResponse(Response $response): never
    {
        $status = $response->status();
        $body = $response->json();
        $message = $body['message'] ?? 'NumHub API error';

        Log::error('NumHub API error', [
            'status' => $status,
            'message' => $message,
            'body' => $body,
        ]);

        throw match (true) {
            $status === 401 => new AuthenticationException($message),
            $status === 403 => new AuthorizationException($message),
            $status === 404 => new EntityNotFoundException($message),
            $status === 429 => new RateLimitException($message, (int) $response->header('Retry-After', 60)),
            $status >= 400 && $status < 500 => new ValidationException($message, $body['errors'] ?? []),
            default => new ApiException($message, $status),
        };
    }

    /**
     * Format filter array for API request.
     *
     * Converts DateTimeInterface to ISO8601 strings.
     *
     * @param array $filters Raw filters
     *
     * @return array Formatted filters
     */
    protected function formatFilters(array $filters): array
    {
        foreach ($filters as $key => $value) {
            if ($value instanceof \DateTimeInterface) {
                $filters[$key] = $value->format(\DateTimeInterface::ATOM);
            }
        }

        return $filters;
    }
}
