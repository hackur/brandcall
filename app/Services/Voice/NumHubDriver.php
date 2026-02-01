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
 * NumHub Voice Driver.
 *
 * Implements VoiceProvider contract for NumHub BrandControl.
 * Enterprise-grade, full BCID ecosystem, requires sales contact.
 *
 * @see https://numhub.com/branded-calling
 */
class NumHubDriver implements VoiceProvider
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function name(): string
    {
        return 'numhub';
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
            'rich_call_data' => true, // NumHub supports logo display
            'number_purchase' => false, // Bring your own numbers
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
            $response = $this->client()->post('/enterprises/' . $brand->numhub_enterprise_id . '/calls', [
                'from' => $from,
                'to' => $to,
                'call_reason' => $callReason ?? $brand->call_reason,
                'brand_name' => $brand->display_name,
                'logo_url' => $brand->logo_url,
                'attestation_level' => $brand->default_attestation_level ?? 'B',
                'rich_call_data' => $brand->rich_call_data ?? [],
                'callback_url' => $options['webhook_url'] ?? route('webhooks.numhub'),
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'call_sid' => $response->json('call_sid'),
                    'status' => $response->json('status', 'initiated'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('NumHub call failed', ['error' => $e->getMessage()]);

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
                return [
                    'success' => true,
                    'status' => $response->json('status'),
                    'duration' => $response->json('duration'),
                ];
            }

            return ['success' => false, 'error' => $response->json('message')];
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
            $response = $this->client()->post('/calls/' . $callSid . '/hangup');

            return ['success' => $response->successful()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function registerBrand(Brand $brand): array
    {
        if ($this->isMockMode()) {
            return $this->mockRegisterBrand($brand);
        }

        try {
            $response = $this->client()->post('/enterprises', [
                'legal_business_name' => $brand->tenant->name,
                'display_name' => $brand->display_name,
                'brand_name' => $brand->name,
                'logo_url' => $brand->logo_url,
                'callback_url' => route('webhooks.numhub'),
                'contact' => [
                    'email' => $brand->tenant->email,
                ],
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'provider_id' => $response->json('enterprise_id'),
                    'vetting_status' => $response->json('vetting_status', 'pending'),
                ];
            }

            return ['success' => false, 'error' => $response->json('message')];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function registerNumber(BrandPhoneNumber $phoneNumber): array
    {
        if ($this->isMockMode()) {
            return ['success' => true, 'attestation_level' => 'B'];
        }

        $brand = $phoneNumber->brand;

        try {
            $response = $this->client()->post('/enterprises/' . $brand->numhub_enterprise_id . '/numbers', [
                'phone_number' => $phoneNumber->phone_number,
                'cnam_display_name' => $phoneNumber->cnam_display_name ?? $brand->display_name,
                'attestation_level' => $brand->default_attestation_level ?? 'B',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'attestation_level' => $response->json('attestation_level'),
                ];
            }

            return ['success' => false, 'error' => $response->json('message')];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function updateCnam(string $phoneNumber, string $callerName): array
    {
        if ($this->isMockMode()) {
            return ['success' => true];
        }

        // NumHub CNAM updates go through number registration
        return ['success' => true, 'note' => 'Update via registerNumber'];
    }

    public function verifyWebhook(string $payload, string $signature, array $headers = []): bool
    {
        if ($this->isMockMode()) {
            return true;
        }

        $secret = $this->config['webhook_secret'] ?? '';

        if (! $secret) {
            return true;
        }

        $expected = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $signature);
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->config['base_url'] ?? 'https://api.numhub.com/v1')
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->config['api_key'],
                'X-API-Secret' => $this->config['api_secret'] ?? '',
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
        Log::info('NumHub MOCK call', compact('from', 'to', 'callReason'));

        return [
            'success' => true,
            'call_sid' => 'NH_CALL_' . Str::upper(Str::random(16)),
            'status' => 'initiated',
        ];
    }

    private function mockRegisterBrand(Brand $brand): array
    {
        Log::info('NumHub MOCK registerBrand', ['brand_id' => $brand->id]);

        return [
            'success' => true,
            'provider_id' => 'NH_ENT_' . Str::upper(Str::random(12)),
            'vetting_status' => 'pending',
        ];
    }
}
