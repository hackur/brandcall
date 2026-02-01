<?php

declare(strict_types=1);

namespace App\Facades;

use App\Contracts\VoiceProvider;
use App\Services\Voice\VoiceManager;
use Illuminate\Support\Facades\Facade;

/**
 * Voice Facade.
 *
 * Provides static access to the VoiceManager.
 *
 * @method static VoiceProvider driver(string|null $driver = null)
 * @method static string        name()
 * @method static bool          isConfigured()
 * @method static array         features()
 * @method static array         call(\App\Models\Brand $brand, string $from, string $to, ?string $callReason = null, array $options = [])
 * @method static array         getCallStatus(string $callSid)
 * @method static array         hangup(string $callSid)
 * @method static array         registerBrand(\App\Models\Brand $brand)
 * @method static array         registerNumber(\App\Models\BrandPhoneNumber $phoneNumber)
 * @method static array         updateCnam(string $phoneNumber, string $callerName)
 * @method static bool          verifyWebhook(string $payload, string $signature, array $headers = [])
 *
 * @see VoiceManager
 */
class Voice extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'voice';
    }
}
