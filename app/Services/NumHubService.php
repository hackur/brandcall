<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Brand;
use App\Models\BrandPhoneNumber;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * NumHub BrandControl API Service.
 *
 * Handles all interactions with the NumHub BrandControl platform for:
 * - Brand/Enterprise registration
 * - Phone number registration and attestation
 * - Branded call initiation
 * - Webhook processing
 *
 * @see https://numhub.com/branded-calling
 */
class NumHubService
{
    private string $baseUrl;

    private string $apiKey;

    private string $apiSecret;

    private bool $useMock;

    public function __construct()
    {
        $this->baseUrl = config('services.numhub.base_url', 'https://api.numhub.com/v1');
        $this->apiKey = config('services.numhub.api_key', '');
        $this->apiSecret = config('services.numhub.api_secret', '');
        $this->useMock = config('services.numhub.use_mock', true);
    }

    /**
     * Create an HTTP client with authentication headers.
     */
    private function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'X-API-Secret' => $this->apiSecret,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout(30);
    }

    /**
     * Register a brand/enterprise with NumHub.
     *
     * @param Brand $brand The brand to register
     *
     * @return array{success: bool, enterprise_id?: string, error?: string}
     */
    public function registerBrand(Brand $brand): array
    {
        if ($this->useMock) {
            return $this->mockRegisterBrand($brand);
        }

        try {
            $response = $this->client()->post('/enterprises', [
                'legal_business_name' => $brand->tenant->name,
                'display_name' => $brand->display_name,
                'brand_name' => $brand->name,
                'logo_url' => $brand->logo_url,
                'website' => $brand->tenant->website ?? null,
                'callback_url' => route('webhooks.numhub'),
                'contact' => [
                    'email' => $brand->tenant->email,
                    'phone' => $brand->tenant->phone ?? null,
                ],
            ]);

            $this->logApiCall('POST /enterprises', $response);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'enterprise_id' => $response->json('enterprise_id'),
                    'vetting_status' => $response->json('vetting_status', 'pending'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('NumHub registerBrand failed', [
                'brand_id' => $brand->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Register a phone number for STIR/SHAKEN attestation.
     *
     * @param BrandPhoneNumber $phoneNumber The phone number to register
     *
     * @return array{success: bool, attestation_level?: string, error?: string}
     */
    public function registerPhoneNumber(BrandPhoneNumber $phoneNumber): array
    {
        if ($this->useMock) {
            return $this->mockRegisterPhoneNumber($phoneNumber);
        }

        $brand = $phoneNumber->brand;

        try {
            $response = $this->client()->post('/enterprises/' . $brand->numhub_enterprise_id . '/numbers', [
                'phone_number' => $phoneNumber->phone_number,
                'country_code' => $phoneNumber->country_code ?? 'US',
                'cnam_display_name' => $phoneNumber->cnam_display_name ?? $brand->display_name,
                'attestation_level' => $brand->default_attestation_level ?? 'B',
                'loa_document_url' => $phoneNumber->loa_document_url ?? null,
            ]);

            $this->logApiCall('POST /enterprises/{id}/numbers', $response);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'number_id' => $response->json('number_id'),
                    'attestation_level' => $response->json('attestation_level'),
                    'status' => $response->json('status', 'pending'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('NumHub registerPhoneNumber failed', [
                'phone_number_id' => $phoneNumber->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Initiate a branded call via NumHub.
     *
     * @param Brand       $brand      The brand making the call
     * @param string      $from       The originating phone number (E.164)
     * @param string      $to         The destination phone number (E.164)
     * @param string|null $callReason Optional call reason to display
     * @param array       $metadata   Additional metadata
     *
     * @return array{success: bool, call_sid?: string, error?: string}
     */
    public function initiateCall(
        Brand $brand,
        string $from,
        string $to,
        ?string $callReason = null,
        array $metadata = []
    ): array {
        if ($this->useMock) {
            return $this->mockInitiateCall($brand, $from, $to, $callReason);
        }

        try {
            $response = $this->client()->post('/enterprises/' . $brand->numhub_enterprise_id . '/calls', [
                'from' => $from,
                'to' => $to,
                'call_reason' => $callReason ?? $brand->call_reason,
                'brand_name' => $brand->display_name,
                'logo_url' => $brand->logo_url,
                'attestation_level' => $brand->default_attestation_level ?? 'B',
                'rich_call_data' => $brand->rich_call_data ?? [],
                'metadata' => $metadata,
                'callback_url' => route('webhooks.numhub'),
            ]);

            $this->logApiCall('POST /enterprises/{id}/calls', $response);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'call_sid' => $response->json('call_sid'),
                    'status' => $response->json('status', 'initiated'),
                    'attestation_level' => $response->json('attestation_level'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('NumHub initiateCall failed', [
                'brand_id' => $brand->id,
                'from' => $from,
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get call status from NumHub.
     *
     * @param string $callSid The NumHub call SID
     *
     * @return array{success: bool, status?: string, error?: string}
     */
    public function getCallStatus(string $callSid): array
    {
        if ($this->useMock) {
            return $this->mockGetCallStatus($callSid);
        }

        try {
            $response = $this->client()->get('/calls/' . $callSid);

            $this->logApiCall('GET /calls/{id}', $response);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->json('status'),
                    'duration' => $response->json('duration'),
                    'answered_at' => $response->json('answered_at'),
                    'ended_at' => $response->json('ended_at'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a webhook signature from NumHub.
     *
     * @param string $payload   The raw webhook payload
     * @param string $signature The signature from X-NumHub-Signature header
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        if ($this->useMock) {
            return true;
        }

        $webhookSecret = config('services.numhub.webhook_secret', '');
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Log API call for debugging.
     */
    private function logApiCall(string $endpoint, Response $response): void
    {
        Log::info('NumHub API call', [
            'endpoint' => $endpoint,
            'status' => $response->status(),
            'success' => $response->successful(),
        ]);
    }

    // =========================================================================
    // Mock Responses (for development without API credentials)
    // =========================================================================

    private function mockRegisterBrand(Brand $brand): array
    {
        Log::info('NumHub MOCK: registerBrand', ['brand_id' => $brand->id]);

        return [
            'success' => true,
            'enterprise_id' => 'NH_ENT_' . Str::upper(Str::random(12)),
            'vetting_status' => 'pending',
        ];
    }

    private function mockRegisterPhoneNumber(BrandPhoneNumber $phoneNumber): array
    {
        Log::info('NumHub MOCK: registerPhoneNumber', ['phone' => $phoneNumber->phone_number]);

        return [
            'success' => true,
            'number_id' => 'NH_NUM_' . Str::upper(Str::random(8)),
            'attestation_level' => 'B',
            'status' => 'active',
        ];
    }

    private function mockInitiateCall(Brand $brand, string $from, string $to, ?string $callReason): array
    {
        Log::info('NumHub MOCK: initiateCall', [
            'brand_id' => $brand->id,
            'from' => $from,
            'to' => $to,
        ]);

        return [
            'success' => true,
            'call_sid' => 'NH_CALL_' . Str::upper(Str::random(16)),
            'status' => 'initiated',
            'attestation_level' => $brand->default_attestation_level ?? 'B',
        ];
    }

    private function mockGetCallStatus(string $callSid): array
    {
        return [
            'success' => true,
            'status' => 'completed',
            'duration' => rand(30, 300),
            'answered_at' => now()->subMinutes(5)->toIso8601String(),
            'ended_at' => now()->toIso8601String(),
        ];
    }
}
