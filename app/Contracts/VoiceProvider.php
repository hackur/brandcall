<?php

/**
 * BrandCall - Branded Caller ID SaaS Platform.
 *
 * @author     BrandCall Development Team
 * @copyright  2024-2026 BrandCall
 * @license    Proprietary
 */

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Brand;
use App\Models\BrandPhoneNumber;

/**
 * Voice Provider Contract - Interface for telephony service providers.
 *
 * All voice providers (NumHub, Telnyx, Twilio, etc.) must implement this interface.
 * This abstraction allows the application to:
 *
 * - Swap providers without changing application code
 * - Support multiple providers simultaneously
 * - Implement provider-specific features gracefully
 * - Test with mock implementations
 *
 * Provider Implementations:
 * - NumHubDriver: Primary provider for branded caller ID (Rich Call Data)
 * - TwilioDriver: Fallback provider with STIR/SHAKEN support
 * - TelnyxDriver: Alternative provider with competitive rates
 * - NullDriver: Testing/development mock
 *
 * Phone Number Format:
 * All phone numbers MUST be in E.164 format (e.g., +14155551234).
 *
 * STIR/SHAKEN Compliance:
 * Providers must support STIR/SHAKEN attestation levels:
 * - A: Full attestation (carrier verified caller owns number)
 * - B: Partial attestation (carrier verified caller relationship)
 * - C: Gateway attestation (no verification)
 *
 * @see \App\Services\Voice\VoiceManager For provider management
 * @see \App\Services\Voice\NumHubDriver For primary implementation
 */
interface VoiceProvider
{
    /**
     * Get the provider identifier name.
     *
     * Used for logging, configuration, and driver resolution.
     * Must be a unique, lowercase string (e.g., 'numhub', 'twilio').
     *
     * @return string Provider identifier
     */
    public function name(): string;

    /**
     * Check if the provider is properly configured and ready to use.
     *
     * Validates that all required configuration values (API keys,
     * credentials, endpoints) are present and valid.
     *
     * @return bool True if provider is ready for API calls
     */
    public function isConfigured(): bool;

    /**
     * Initiate an outbound branded call.
     *
     * Places a call through the provider's network with branded
     * caller ID information. The brand's display name and logo
     * (if supported via Rich Call Data) will be shown to the recipient.
     *
     * Call Flow:
     * 1. Validate brand and phone numbers
     * 2. Apply STIR/SHAKEN attestation
     * 3. Include Rich Call Data (if supported)
     * 4. Initiate call via provider API
     * 5. Return call identifier for tracking
     *
     * @param Brand                $brand      The brand making the call (contains display info)
     * @param string               $from       Originating phone number (E.164 format, must be registered)
     * @param string               $to         Destination phone number (E.164 format)
     * @param string|null          $callReason Optional call reason/purpose to display on device
     * @param array<string, mixed> $options    Additional provider-specific options:
     *                                         - 'attestation': STIR/SHAKEN level (A, B, C)
     *                                         - 'callback_url': Webhook for call events
     *                                         - 'record': bool to enable recording
     *                                         - 'timeout': Ring timeout in seconds
     *
     * @return array{success: bool, call_sid?: string, status?: string, error?: string}
     *                                                                                  - success: Whether call was initiated
     *                                                                                  - call_sid: Provider's unique call identifier
     *                                                                                  - status: Initial call status
     *                                                                                  - error: Error message if failed
     */
    public function call(
        Brand $brand,
        string $from,
        string $to,
        ?string $callReason = null,
        array $options = []
    ): array;

    /**
     * Get the current status of a call.
     *
     * Retrieves real-time status information for an active or
     * completed call. Used for call tracking and analytics.
     *
     * Possible statuses:
     * - queued: Call is queued for processing
     * - ringing: Destination phone is ringing
     * - in-progress: Call is connected
     * - completed: Call ended normally
     * - busy: Destination was busy
     * - no-answer: No answer within timeout
     * - failed: Call failed to connect
     * - canceled: Call was canceled before connecting
     *
     * @param string $callSid The provider's unique call identifier
     *
     * @return array{success: bool, status?: string, duration?: int, error?: string}
     *                                                                               - success: Whether status was retrieved
     *                                                                               - status: Current call status
     *                                                                               - duration: Call duration in seconds (if completed)
     *                                                                               - error: Error message if failed
     */
    public function getCallStatus(string $callSid): array;

    /**
     * Terminate an active call.
     *
     * Immediately ends a call in progress. Used for:
     * - User-initiated hangup
     * - Timeout enforcement
     * - Emergency call termination
     *
     * @param string $callSid The provider's unique call identifier
     *
     * @return array{success: bool, error?: string}
     *                                              - success: Whether call was terminated
     *                                              - error: Error message if failed
     */
    public function hangup(string $callSid): array;

    /**
     * Register a brand with the voice provider.
     *
     * Creates or updates the brand's profile with the provider,
     * enabling branded caller ID for calls made by this brand.
     *
     * Registration typically includes:
     * - Brand display name
     * - Brand logo URL
     * - Business verification status
     * - Allowed phone numbers
     *
     * Some providers require brand registration before calls
     * can display branded information.
     *
     * @param Brand $brand The brand to register
     *
     * @return array{success: bool, provider_id?: string, error?: string}
     *                                                                    - success: Whether registration succeeded
     *                                                                    - provider_id: Provider's unique brand identifier
     *                                                                    - error: Error message if failed
     */
    public function registerBrand(Brand $brand): array;

    /**
     * Register a phone number for branded calling.
     *
     * Associates a phone number with a brand for outbound calling.
     * The number must be verified as belonging to the tenant before
     * it can be used for branded calls.
     *
     * Registration process:
     * 1. Verify number ownership (via LOA or carrier verification)
     * 2. Register with provider's number registry
     * 3. Enable STIR/SHAKEN signing for the number
     * 4. Associate with brand for caller ID display
     *
     * @param BrandPhoneNumber $phoneNumber The phone number to register
     *
     * @return array{success: bool, error?: string}
     *                                              - success: Whether registration succeeded
     *                                              - error: Error message if failed
     */
    public function registerNumber(BrandPhoneNumber $phoneNumber): array;

    /**
     * Update CNAM (Caller Name) for a phone number.
     *
     * Sets the caller name that appears on recipient's caller ID
     * when calls are made from this number.
     *
     * CNAM Limitations:
     * - Maximum 15 characters typically
     * - Alphanumeric and limited punctuation
     * - Propagation can take 24-72 hours
     * - Not displayed on all carriers/devices
     *
     * @param string $phoneNumber Phone number in E.164 format
     * @param string $callerName  Caller name (max 15 chars recommended)
     *
     * @return array{success: bool, error?: string}
     *                                              - success: Whether update was accepted
     *                                              - error: Error message if failed
     */
    public function updateCnam(string $phoneNumber, string $callerName): array;

    /**
     * Verify a webhook signature from this provider.
     *
     * Validates that an incoming webhook request originated from
     * the provider and hasn't been tampered with. Essential for
     * security when processing call events.
     *
     * Verification methods vary by provider:
     * - HMAC signature validation
     * - Public key verification
     * - Token validation
     *
     * @param string                $payload   Raw webhook request body
     * @param string                $signature Signature from request header
     * @param array<string, string> $headers   All request headers (some providers need multiple)
     *
     * @return bool True if webhook is valid and trusted
     */
    public function verifyWebhook(string $payload, string $signature, array $headers = []): bool;

    /**
     * Get available features for this provider.
     *
     * Returns capabilities supported by this provider. Used to:
     * - Display available options in UI
     * - Conditionally enable features
     * - Select appropriate provider for a use case
     *
     * @return array{stir_shaken: bool, cnam: bool, rich_call_data: bool, number_purchase: bool}
     *                                                                                           - stir_shaken: Supports STIR/SHAKEN attestation
     *                                                                                           - cnam: Supports CNAM (Caller Name) updates
     *                                                                                           - rich_call_data: Supports RCD (logo, call reason)
     *                                                                                           - number_purchase: Can provision new phone numbers
     */
    public function features(): array;
}
