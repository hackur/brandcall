<?php

/**
 * BrandCall - Branded Caller ID SaaS Platform.
 *
 * @author     BrandCall Development Team
 * @copyright  2024-2026 BrandCall
 * @license    Proprietary
 */

declare(strict_types=1);

namespace App\Services\Voice;

use App\Contracts\VoiceProvider;
use Closure;
use Illuminate\Support\Manager;
use InvalidArgumentException;

/**
 * Voice Manager - Laravel-style manager for voice provider drivers.
 *
 * Provides a unified interface for interacting with multiple telephony
 * providers. Implements the Manager pattern to allow runtime driver
 * selection and easy provider swapping.
 *
 * Supported Drivers:
 * - telnyx: Telnyx communications platform
 * - numhub: NumHub BrandControl for enterprise branded calling
 * - twilio: Twilio programmable voice
 * - null: Mock driver for testing
 *
 * Usage Examples:
 * ```php
 * // Use default driver (from config)
 * $result = Voice::call($brand, $from, $to, $reason);
 *
 * // Use specific driver
 * $result = Voice::driver('telnyx')->call($brand, $from, $to, $reason);
 *
 * // Check if provider is configured
 * if (Voice::isConfigured()) {
 *     // Make calls
 * }
 *
 * // Get available features
 * $features = Voice::features();
 * ```
 *
 * Configuration (config/voice.php):
 * ```php
 * return [
 *     'default' => env('VOICE_DRIVER', 'telnyx'),
 *     'drivers' => [
 *         'telnyx' => ['api_key' => env('TELNYX_API_KEY')],
 *         'numhub' => ['api_key' => env('NUMHUB_API_KEY')],
 *         'twilio' => ['sid' => env('TWILIO_SID'), ...],
 *     ],
 * ];
 * ```
 *
 * @see \App\Contracts\VoiceProvider Interface for all drivers
 * @see \App\Facades\Voice Facade for static access
 *
 * @method array  call(\App\Models\Brand $brand, string $from, string $to, ?string $callReason = null, array $options = [])
 * @method array  getCallStatus(string $callSid)
 * @method array  hangup(string $callSid)
 * @method array  registerBrand(\App\Models\Brand $brand)
 * @method array  registerNumber(\App\Models\BrandPhoneNumber $phoneNumber)
 * @method array  updateCnam(string $phoneNumber, string $callerName)
 * @method bool   verifyWebhook(string $payload, string $signature, array $headers = [])
 * @method array  features()
 * @method bool   isConfigured()
 * @method string name()
 */
class VoiceManager extends Manager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('voice.default', 'telnyx');
    }

    /**
     * Create a Telnyx driver instance.
     */
    protected function createTelnyxDriver(): VoiceProvider
    {
        $config = $this->config->get('voice.drivers.telnyx', []);

        return new TelnyxDriver($config);
    }

    /**
     * Create a NumHub driver instance.
     */
    protected function createNumhubDriver(): VoiceProvider
    {
        $config = $this->config->get('voice.drivers.numhub', []);

        return new NumHubDriver($config);
    }

    /**
     * Create a Twilio driver instance.
     */
    protected function createTwilioDriver(): VoiceProvider
    {
        $config = $this->config->get('voice.drivers.twilio', []);

        return new TwilioDriver($config);
    }

    /**
     * Create a null/mock driver for testing.
     */
    protected function createNullDriver(): VoiceProvider
    {
        return new NullDriver;
    }

    /**
     * Register a custom driver creator.
     *
     * @param string  $driver   Driver name
     * @param Closure $callback Factory callback
     *
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Get a driver instance.
     *
     * @param string|null $driver
     *
     * @throws InvalidArgumentException
     */
    public function driver($driver = null): VoiceProvider
    {
        return parent::driver($driver);
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}
