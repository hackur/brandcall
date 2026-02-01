<?php

declare(strict_types=1);

namespace App\Services\Voice;

use App\Contracts\VoiceProvider;
use App\Models\Brand;
use App\Models\BrandPhoneNumber;
use Illuminate\Support\Str;

/**
 * Null Voice Driver.
 *
 * A no-op driver for testing. Always succeeds, logs nothing, does nothing.
 */
class NullDriver implements VoiceProvider
{
    public function name(): string
    {
        return 'null';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function features(): array
    {
        return [
            'stir_shaken' => false,
            'cnam' => false,
            'rich_call_data' => false,
            'number_purchase' => false,
        ];
    }

    public function call(
        Brand $brand,
        string $from,
        string $to,
        ?string $callReason = null,
        array $options = []
    ): array {
        return [
            'success' => true,
            'call_sid' => 'null_' . Str::random(20),
            'status' => 'initiated',
        ];
    }

    public function getCallStatus(string $callSid): array
    {
        return [
            'success' => true,
            'status' => 'completed',
            'duration' => 0,
        ];
    }

    public function hangup(string $callSid): array
    {
        return ['success' => true];
    }

    public function registerBrand(Brand $brand): array
    {
        return ['success' => true, 'provider_id' => 'null'];
    }

    public function registerNumber(BrandPhoneNumber $phoneNumber): array
    {
        return ['success' => true];
    }

    public function updateCnam(string $phoneNumber, string $callerName): array
    {
        return ['success' => true];
    }

    public function verifyWebhook(string $payload, string $signature, array $headers = []): bool
    {
        return true;
    }
}
