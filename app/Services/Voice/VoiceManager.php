<?php

declare(strict_types=1);

namespace App\Services\Voice;

use App\Contracts\VoiceProvider;
use Closure;
use Illuminate\Support\Manager;
use InvalidArgumentException;

/**
 * Voice Manager.
 *
 * Laravel-style manager for voice providers. Supports multiple drivers
 * that can be swapped via configuration.
 *
 * Usage:
 *   // Use default driver
 *   $result = Voice::call($brand, $from, $to, $reason);
 *
 *   // Use specific driver
 *   $result = Voice::driver('telnyx')->call($brand, $from, $to, $reason);
 *
 * Configuration (config/voice.php):
 *   'default' => env('VOICE_DRIVER', 'telnyx'),
 *   'drivers' => [
 *       'telnyx' => [...],
 *       'numhub' => [...],
 *       'twilio' => [...],
 *   ]
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
