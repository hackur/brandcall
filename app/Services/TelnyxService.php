<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Brand;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Telnyx Voice API Service.
 *
 * Alternative to NumHub for branded calling. Telnyx provides:
 * - STIR/SHAKEN call authentication
 * - CNAM (Caller Name) display
 * - Dynamic caller ID
 * - $0.002/min starting (very cheap)
 *
 * @see https://developers.telnyx.com/docs/voice/programmable-voice
 */
class TelnyxService
{
    private string $baseUrl = 'https://api.telnyx.com/v2';

    private string $apiKey;

    private bool $enabled;

    private bool $useMock;

    public function __construct()
    {
        $this->apiKey = config('services.telnyx.api_key', '');
        $this->enabled = ! empty($this->apiKey);
        $this->useMock = config('services.telnyx.use_mock', true);
    }

    /**
     * Check if Telnyx is configured.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Create an HTTP client with authentication.
     */
    private function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout(30);
    }

    /**
     * Initiate an outbound call with caller ID.
     *
     * @param Brand       $brand      The brand making the call
     * @param string      $from       The originating phone number (E.164)
     * @param string      $to         The destination phone number (E.164)
     * @param string|null $callReason Optional call reason
     * @param array       $options    Additional options
     *
     * @return array{success: bool, call_control_id?: string, error?: string}
     */
    public function initiateCall(
        Brand $brand,
        string $from,
        string $to,
        ?string $callReason = null,
        array $options = []
    ): array {
        if ($this->useMock) {
            return $this->mockInitiateCall($brand, $from, $to, $callReason);
        }

        if (! $this->enabled) {
            return [
                'success' => false,
                'error' => 'Telnyx not configured',
            ];
        }

        try {
            $connectionId = config('services.telnyx.connection_id');

            if (! $connectionId) {
                return [
                    'success' => false,
                    'error' => 'Telnyx connection_id not configured',
                ];
            }

            $response = $this->client()->post('/calls', [
                'to' => $to,
                'from' => $from,
                'from_display_name' => $brand->display_name, // CNAM display
                'connection_id' => $connectionId,
                'webhook_url' => route('webhooks.telnyx'),
                'client_state' => base64_encode(json_encode([
                    'brand_id' => $brand->id,
                    'tenant_id' => $brand->tenant_id,
                    'call_reason' => $callReason,
                ])),
                'custom_headers' => [
                    [
                        'name' => 'X-Call-Reason',
                        'value' => $callReason ?? $brand->call_reason ?? 'Business Call',
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json('data');

                Log::info('Telnyx call initiated', [
                    'brand_id' => $brand->id,
                    'call_control_id' => $data['call_control_id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'call_control_id' => $data['call_control_id'],
                    'call_leg_id' => $data['call_leg_id'] ?? null,
                    'call_session_id' => $data['call_session_id'] ?? null,
                    'status' => 'initiated',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('errors.0.detail', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('Telnyx call initiation failed', [
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
     * Get call details.
     */
    public function getCall(string $callControlId): array
    {
        if ($this->useMock) {
            return $this->mockGetCall($callControlId);
        }

        try {
            $response = $this->client()->get('/calls/' . $callControlId);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json('data'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('errors.0.detail', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Hang up a call.
     */
    public function hangup(string $callControlId): array
    {
        if ($this->useMock) {
            return ['success' => true];
        }

        try {
            $response = $this->client()->post('/calls/' . $callControlId . '/actions/hangup');

            return [
                'success' => $response->successful(),
                'error' => $response->successful() ? null : $response->json('errors.0.detail'),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * List available phone numbers.
     */
    public function listPhoneNumbers(): array
    {
        if ($this->useMock) {
            return $this->mockListPhoneNumbers();
        }

        try {
            $response = $this->client()->get('/phone_numbers', [
                'page[size]' => 100,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'numbers' => collect($response->json('data'))->map(fn ($n) => [
                        'id' => $n['id'],
                        'phone_number' => $n['phone_number'],
                        'status' => $n['status'],
                        'connection_id' => $n['connection_id'] ?? null,
                    ])->toArray(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('errors.0.detail', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Search for available phone numbers to purchase.
     */
    public function searchNumbers(string $country = 'US', ?string $areaCode = null): array
    {
        if ($this->useMock) {
            return $this->mockSearchNumbers($country, $areaCode);
        }

        try {
            $params = [
                'filter[country_code]' => $country,
                'filter[features][]' => 'voice',
                'page[size]' => 20,
            ];

            if ($areaCode) {
                $params['filter[national_destination_code]'] = $areaCode;
            }

            $response = $this->client()->get('/available_phone_numbers', $params);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'numbers' => collect($response->json('data'))->map(fn ($n) => [
                        'phone_number' => $n['phone_number'],
                        'region' => $n['region_information'][0]['region_name'] ?? null,
                        'monthly_cost' => $n['cost_information']['monthly_cost'] ?? null,
                        'features' => $n['features'] ?? [],
                    ])->toArray(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('errors.0.detail', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Purchase a phone number.
     */
    public function purchaseNumber(string $phoneNumber, string $connectionId): array
    {
        if ($this->useMock) {
            return $this->mockPurchaseNumber($phoneNumber);
        }

        try {
            $response = $this->client()->post('/number_orders', [
                'phone_numbers' => [
                    ['phone_number' => $phoneNumber],
                ],
                'connection_id' => $connectionId,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'order_id' => $response->json('data.id'),
                    'status' => $response->json('data.status'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('errors.0.detail', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update CNAM (Caller Name) for a number.
     */
    public function updateCnam(string $phoneNumberId, string $callerName): array
    {
        if ($this->useMock) {
            return ['success' => true, 'cnam' => $callerName];
        }

        try {
            // CNAM updates go through the phone number update endpoint
            $response = $this->client()->patch('/phone_numbers/' . $phoneNumberId, [
                'cnam_listing_enabled' => true,
                'cnam_listing_details' => $callerName,
            ]);

            if ($response->successful()) {
                Log::info('CNAM updated', [
                    'phone_number_id' => $phoneNumberId,
                    'cnam' => $callerName,
                ]);

                return [
                    'success' => true,
                    'cnam' => $callerName,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('errors.0.detail', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $signature, string $timestamp): bool
    {
        if ($this->useMock) {
            return true;
        }

        $publicKey = config('services.telnyx.public_key');

        if (! $publicKey) {
            Log::warning('Telnyx public key not configured');

            return true; // Allow in dev
        }

        // Telnyx uses ed25519 signatures
        $signedPayload = $timestamp . '|' . $payload;

        return sodium_crypto_sign_verify_detached(
            base64_decode($signature),
            $signedPayload,
            base64_decode($publicKey)
        );
    }

    // =========================================================================
    // Mock Responses
    // =========================================================================

    private function mockInitiateCall(Brand $brand, string $from, string $to, ?string $callReason): array
    {
        Log::info('Telnyx MOCK: initiateCall', [
            'brand_id' => $brand->id,
            'from' => $from,
            'to' => $to,
        ]);

        return [
            'success' => true,
            'call_control_id' => 'v3:' . Str::random(40),
            'call_leg_id' => Str::uuid()->toString(),
            'call_session_id' => Str::uuid()->toString(),
            'status' => 'initiated',
        ];
    }

    private function mockGetCall(string $callControlId): array
    {
        return [
            'success' => true,
            'data' => [
                'call_control_id' => $callControlId,
                'state' => 'active',
                'is_alive' => true,
            ],
        ];
    }

    private function mockListPhoneNumbers(): array
    {
        return [
            'success' => true,
            'numbers' => [
                [
                    'id' => 'mock-123',
                    'phone_number' => '+18005551234',
                    'status' => 'active',
                    'connection_id' => 'mock-conn-456',
                ],
            ],
        ];
    }

    private function mockSearchNumbers(string $country, ?string $areaCode): array
    {
        return [
            'success' => true,
            'numbers' => [
                [
                    'phone_number' => '+1' . ($areaCode ?? '702') . '5551234',
                    'region' => 'Nevada',
                    'monthly_cost' => '1.00',
                    'features' => ['voice', 'sms'],
                ],
                [
                    'phone_number' => '+1' . ($areaCode ?? '702') . '5555678',
                    'region' => 'Nevada',
                    'monthly_cost' => '1.00',
                    'features' => ['voice'],
                ],
            ],
        ];
    }

    private function mockPurchaseNumber(string $phoneNumber): array
    {
        return [
            'success' => true,
            'order_id' => 'order-' . Str::random(10),
            'status' => 'pending',
        ];
    }
}
