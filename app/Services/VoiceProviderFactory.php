<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\Log;

/**
 * Voice Provider Factory.
 *
 * Abstracts the voice provider (NumHub or Telnyx) so we can switch
 * between providers easily via configuration.
 *
 * Usage:
 *   $provider = app(VoiceProviderFactory::class);
 *   $result = $provider->initiateCall($brand, $from, $to, $callReason);
 */
class VoiceProviderFactory
{
    private string $provider;

    private NumHubService $numhub;

    private TelnyxService $telnyx;

    public function __construct(NumHubService $numhub, TelnyxService $telnyx)
    {
        $this->provider = config('services.voice_provider', 'telnyx');
        $this->numhub = $numhub;
        $this->telnyx = $telnyx;
    }

    /**
     * Get the current provider name.
     */
    public function getProviderName(): string
    {
        return $this->provider;
    }

    /**
     * Check if the current provider is configured and enabled.
     */
    public function isEnabled(): bool
    {
        return match ($this->provider) {
            'numhub' => true, // NumHub has mock mode
            'telnyx' => true, // Telnyx has mock mode
            default => false,
        };
    }

    /**
     * Initiate a branded call using the configured provider.
     *
     * @param Brand       $brand      The brand making the call
     * @param string      $from       The originating phone number (E.164)
     * @param string      $to         The destination phone number (E.164)
     * @param string|null $callReason Optional call reason to display
     * @param array       $metadata   Additional metadata
     *
     * @return array{success: bool, call_sid?: string, call_control_id?: string, error?: string}
     */
    public function initiateCall(
        Brand $brand,
        string $from,
        string $to,
        ?string $callReason = null,
        array $metadata = []
    ): array {
        Log::info('VoiceProvider initiateCall', [
            'provider' => $this->provider,
            'brand_id' => $brand->id,
            'from' => $from,
            'to' => $to,
        ]);

        $result = match ($this->provider) {
            'numhub' => $this->numhub->initiateCall($brand, $from, $to, $callReason, $metadata),
            'telnyx' => $this->telnyx->initiateCall($brand, $from, $to, $callReason, $metadata),
            default => ['success' => false, 'error' => 'Unknown voice provider: ' . $this->provider],
        };

        // Normalize the response
        if ($result['success']) {
            return [
                'success' => true,
                'call_sid' => $result['call_sid'] ?? $result['call_control_id'] ?? null,
                'call_control_id' => $result['call_control_id'] ?? $result['call_sid'] ?? null,
                'status' => $result['status'] ?? 'initiated',
                'provider' => $this->provider,
            ];
        }

        return $result;
    }

    /**
     * Register a brand with the provider (if supported).
     */
    public function registerBrand(Brand $brand): array
    {
        return match ($this->provider) {
            'numhub' => $this->numhub->registerBrand($brand),
            'telnyx' => ['success' => true, 'message' => 'Telnyx does not require brand registration'],
            default => ['success' => false, 'error' => 'Unknown provider'],
        };
    }

    /**
     * Get call status from the provider.
     */
    public function getCallStatus(string $callId): array
    {
        return match ($this->provider) {
            'numhub' => $this->numhub->getCallStatus($callId),
            'telnyx' => $this->telnyx->getCall($callId),
            default => ['success' => false, 'error' => 'Unknown provider'],
        };
    }

    /**
     * List available phone numbers.
     */
    public function listPhoneNumbers(): array
    {
        return match ($this->provider) {
            'telnyx' => $this->telnyx->listPhoneNumbers(),
            default => ['success' => false, 'error' => 'Not supported by ' . $this->provider],
        };
    }

    /**
     * Search for available phone numbers to purchase.
     */
    public function searchNumbers(string $country = 'US', ?string $areaCode = null): array
    {
        return match ($this->provider) {
            'telnyx' => $this->telnyx->searchNumbers($country, $areaCode),
            default => ['success' => false, 'error' => 'Not supported by ' . $this->provider],
        };
    }

    /**
     * Verify a webhook signature.
     */
    public function verifyWebhook(string $payload, string $signature, ?string $timestamp = null): bool
    {
        return match ($this->provider) {
            'numhub' => $this->numhub->verifyWebhookSignature($payload, $signature),
            'telnyx' => $this->telnyx->verifyWebhookSignature($payload, $signature, $timestamp ?? ''),
            default => false,
        };
    }
}
