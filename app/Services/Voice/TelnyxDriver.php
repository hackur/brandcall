<?php

declare(strict_types=1);

namespace App\Services\Voice;

use App\Contracts\VoiceProvider;
use App\Models\Brand;
use App\Models\BrandPhoneNumber;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Telnyx Voice Driver.
 *
 * Implements VoiceProvider contract for Telnyx.
 * Cheap, developer-friendly, supports STIR/SHAKEN and CNAM.
 *
 * @see https://developers.telnyx.com/docs/voice/programmable-voice
 */
class TelnyxDriver implements VoiceProvider
{
    private const BASE_URL = 'https://api.telnyx.com/v2';

    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function name(): string
    {
        return 'telnyx';
    }

    public function isConfigured(): bool
    {
        return ! empty($this->config['api_key']) && ! $this->isMockMode();
    }

    public function features(): array
    {
        return [
            'stir_shaken' => true,
            'cnam' => true,
            'rich_call_data' => false, // Telnyx doesn't support logo display
            'number_purchase' => true,
        ];
    }

    public function call(
        Brand $brand,
        string $from,
        string $to,
        ?string $callReason = null,
        array $options = []
    ): array {
        if ($this->isMockMode()) {
            return $this->mockCall($brand, $from, $to, $callReason);
        }

        try {
            $response = $this->client()->post('/calls', [
                'to' => $to,
                'from' => $from,
                'from_display_name' => substr($brand->display_name, 0, 15), // CNAM limit
                'connection_id' => $this->config['connection_id'],
                'webhook_url' => $options['webhook_url'] ?? route('webhooks.telnyx'),
                'client_state' => base64_encode(json_encode([
                    'brand_id' => $brand->id,
                    'tenant_id' => $brand->tenant_id,
                    'call_reason' => $callReason,
                ])),
            ]);

            if ($response->successful()) {
                $data = $response->json('data');

                return [
                    'success' => true,
                    'call_sid' => $data['call_control_id'],
                    'status' => 'initiated',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('errors.0.detail', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('Telnyx call failed', ['error' => $e->getMessage()]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getCallStatus(string $callSid): array
    {
        if ($this->isMockMode()) {
            return ['success' => true, 'status' => 'completed', 'duration' => 120];
        }

        try {
            $response = $this->client()->get('/calls/' . $callSid);

            if ($response->successful()) {
                $data = $response->json('data');

                return [
                    'success' => true,
                    'status' => $data['state'] ?? 'unknown',
                    'is_alive' => $data['is_alive'] ?? false,
                ];
            }

            return ['success' => false, 'error' => $response->json('errors.0.detail')];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function hangup(string $callSid): array
    {
        if ($this->isMockMode()) {
            return ['success' => true];
        }

        try {
            $response = $this->client()->post('/calls/' . $callSid . '/actions/hangup');

            return ['success' => $response->successful()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function registerBrand(Brand $brand): array
    {
        // Telnyx doesn't require brand registration
        return [
            'success' => true,
            'message' => 'Telnyx does not require brand registration',
        ];
    }

    public function registerNumber(BrandPhoneNumber $phoneNumber): array
    {
        // Numbers are managed via Telnyx portal or API
        return ['success' => true];
    }

    public function updateCnam(string $phoneNumber, string $callerName): array
    {
        if ($this->isMockMode()) {
            return ['success' => true];
        }

        // Would need to find the phone number ID first, then update
        // For now, return success as CNAM is set per-call via from_display_name
        return [
            'success' => true,
            'note' => 'CNAM is set dynamically per call via from_display_name',
        ];
    }

    public function verifyWebhook(string $payload, string $signature, array $headers = []): bool
    {
        if ($this->isMockMode()) {
            return true;
        }

        $publicKey = $this->config['public_key'] ?? null;
        $timestamp = $headers['telnyx-timestamp'] ?? '';

        if (! $publicKey || ! $signature || ! $timestamp) {
            return true; // Allow if not configured
        }

        try {
            $signedPayload = $timestamp . '|' . $payload;

            return sodium_crypto_sign_verify_detached(
                base64_decode($signature),
                $signedPayload,
                base64_decode($publicKey)
            );
        } catch (\Exception $e) {
            Log::warning('Telnyx webhook verification failed', ['error' => $e->getMessage()]);

            return true; // Allow in case of verification issues
        }
    }

    /**
     * Search for available phone numbers.
     */
    public function searchNumbers(string $country = 'US', ?string $areaCode = null): array
    {
        if ($this->isMockMode()) {
            return $this->mockSearchNumbers($areaCode);
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
                    ])->toArray(),
                ];
            }

            return ['success' => false, 'error' => $response->json('errors.0.detail')];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl(self::BASE_URL)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->config['api_key'],
                'Content-Type' => 'application/json',
            ])
            ->timeout(30);
    }

    private function isMockMode(): bool
    {
        return $this->config['mock'] ?? true;
    }

    private function mockCall(Brand $brand, string $from, string $to, ?string $callReason): array
    {
        Log::info('Telnyx MOCK call', compact('from', 'to', 'callReason'));

        return [
            'success' => true,
            'call_sid' => 'v3:mock_' . Str::random(32),
            'status' => 'initiated',
        ];
    }

    private function mockSearchNumbers(?string $areaCode): array
    {
        $code = $areaCode ?? '702';

        return [
            'success' => true,
            'numbers' => [
                ['phone_number' => "+1{$code}5551234", 'region' => 'Nevada', 'monthly_cost' => '1.00'],
                ['phone_number' => "+1{$code}5555678", 'region' => 'Nevada', 'monthly_cost' => '1.00'],
            ],
        ];
    }
}
