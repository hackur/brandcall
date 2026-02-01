<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Brand;
use App\Models\BrandPhoneNumber;

/**
 * Voice Provider Contract.
 *
 * All voice providers (NumHub, Telnyx, Twilio, etc.) must implement this interface.
 * This allows swapping providers without changing application code.
 */
interface VoiceProvider
{
    /**
     * Get the provider name.
     */
    public function name(): string;

    /**
     * Check if the provider is properly configured.
     */
    public function isConfigured(): bool;

    /**
     * Initiate an outbound branded call.
     *
     * @param Brand       $brand      The brand making the call
     * @param string      $from       The originating phone number (E.164 format)
     * @param string      $to         The destination phone number (E.164 format)
     * @param string|null $callReason Optional call reason to display
     * @param array       $options    Additional provider-specific options
     *
     * @return array{success: bool, call_sid?: string, status?: string, error?: string}
     */
    public function call(
        Brand $brand,
        string $from,
        string $to,
        ?string $callReason = null,
        array $options = []
    ): array;

    /**
     * Get the status of a call.
     *
     * @param string $callSid The provider's call identifier
     *
     * @return array{success: bool, status?: string, duration?: int, error?: string}
     */
    public function getCallStatus(string $callSid): array;

    /**
     * Hang up an active call.
     *
     * @param string $callSid The provider's call identifier
     *
     * @return array{success: bool, error?: string}
     */
    public function hangup(string $callSid): array;

    /**
     * Register a brand with the provider (if required).
     *
     * @param Brand $brand The brand to register
     *
     * @return array{success: bool, provider_id?: string, error?: string}
     */
    public function registerBrand(Brand $brand): array;

    /**
     * Register a phone number for branded calling.
     *
     * @param BrandPhoneNumber $phoneNumber The phone number to register
     *
     * @return array{success: bool, error?: string}
     */
    public function registerNumber(BrandPhoneNumber $phoneNumber): array;

    /**
     * Update CNAM (Caller Name) for a phone number.
     *
     * @param string $phoneNumber The phone number (E.164)
     * @param string $callerName  The caller name to display (max 15 chars typically)
     *
     * @return array{success: bool, error?: string}
     */
    public function updateCnam(string $phoneNumber, string $callerName): array;

    /**
     * Verify a webhook signature from this provider.
     *
     * @param string $payload   The raw webhook payload
     * @param string $signature The signature header value
     * @param array  $headers   All request headers (some providers need multiple)
     */
    public function verifyWebhook(string $payload, string $signature, array $headers = []): bool;

    /**
     * Get available features for this provider.
     *
     * @return array{stir_shaken: bool, cnam: bool, rich_call_data: bool, number_purchase: bool}
     */
    public function features(): array;
}
