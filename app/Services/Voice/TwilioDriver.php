<?php

declare(strict_types=1);

namespace App\Services\Voice;

use App\Contracts\VoiceProvider;
use App\Models\Brand;
use App\Models\BrandPhoneNumber;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Twilio Voice Driver.
 *
 * Implements VoiceProvider contract for Twilio Branded Calling.
 * Twilio's Branded Calling displays business name, logo, and call reason
 * on recipient devices via STIR/SHAKEN and Rich Call Data.
 *
 * @see https://www.twilio.com/docs/voice/branded-calling
 */
class TwilioDriver implements VoiceProvider
{
    /**
     * @param array{
     *   account_sid?: string,
     *   auth_token?: string,
     *   branded_calling_sid?: string,
     *   webhook_auth_token?: string,
     *   mock?: bool
     * } $config
     */
    public function __construct(
        private readonly array $config = []
    ) {}

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'twilio';
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigured(): bool
    {
        if ($this->config['mock'] ?? false) {
            return true;
        }

        return ! empty($this->config['account_sid'])
            && ! empty($this->config['auth_token'])
            && ! empty($this->config['branded_calling_sid']);
    }

    /**
     * {@inheritdoc}
     */
    public function features(): array
    {
        return [
            'stir_shaken' => true,
            'cnam' => true,
            'rich_call_data' => true,
            'number_purchase' => true,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $options
     */
    public function call(
        Brand $brand,
        string $from,
        string $to,
        ?string $callReason = null,
        array $options = []
    ): array {
        if ($this->config['mock'] ?? false) {
            return $this->mockCall($from, $to, $callReason);
        }

        try {
            $response = $this->client()->post('/Calls.json', [
                'From' => $from,
                'To' => $to,
                'Url' => $options['twiml_url'] ?? route('webhooks.twilio.voice'),
                'StatusCallback' => $options['status_callback'] ?? route('webhooks.twilio.status'),
                'StatusCallbackMethod' => 'POST',
                'StatusCallbackEvent' => ['initiated', 'ringing', 'answered', 'completed'],
                // Branded Calling parameters
                'CallerId' => $from,
                'CallerIdName' => $brand->display_name ?? $brand->name,
                'BrandedCallingSid' => $this->config['branded_calling_sid'],
                'CallReason' => $callReason ?? $brand->call_reason ?? 'Business Call',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'call_sid' => $data['sid'],
                    'status' => $data['status'],
                    'provider' => 'twilio',
                ];
            }

            Log::error('Twilio call failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Twilio API error',
            ];
        } catch (\Exception $e) {
            Log::error('Twilio call exception', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCallStatus(string $callSid): array
    {
        if ($this->config['mock'] ?? false) {
            return [
                'success' => true,
                'status' => 'completed',
                'duration' => 45,
            ];
        }

        try {
            $response = $this->client()->get("/Calls/{$callSid}.json");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'status' => $data['status'],
                    'duration' => (int) ($data['duration'] ?? 0),
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get call status',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hangup(string $callSid): array
    {
        if ($this->config['mock'] ?? false) {
            return ['success' => true];
        }

        try {
            $response = $this->client()->post("/Calls/{$callSid}.json", [
                'Status' => 'completed',
            ]);

            return [
                'success' => $response->successful(),
                'error' => $response->successful() ? null : 'Failed to hangup call',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerBrand(Brand $brand): array
    {
        if ($this->config['mock'] ?? false) {
            Log::info('Twilio MOCK registerBrand', ['brand_id' => $brand->id]);

            return [
                'success' => true,
                'provider_id' => 'twilio_brand_' . uniqid(),
            ];
        }

        // Twilio Branded Calling uses Business Profiles
        // This would register the brand with Twilio's Trust Hub
        try {
            /** @var \App\Models\User|null $firstUser */
            $firstUser = $brand->tenant->users->first();
            $email = $firstUser !== null ? $firstUser->email : 'support@brandcall.io';

            $response = $this->client()->post('/TrustHub/v1/CustomerProfiles', [
                'FriendlyName' => $brand->name,
                'Email' => $email,
                'PolicySid' => 'RN...',  // Twilio's Branded Calling policy SID
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'provider_id' => $response->json()['sid'],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to register brand with Twilio',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerNumber(BrandPhoneNumber $phoneNumber): array
    {
        if ($this->config['mock'] ?? false) {
            return ['success' => true];
        }

        // In Twilio, numbers are typically already registered
        // This would associate the number with a Branded Calling profile
        return ['success' => true];
    }

    /**
     * {@inheritdoc}
     */
    public function updateCnam(string $phoneNumber, string $callerName): array
    {
        if ($this->config['mock'] ?? false) {
            return ['success' => true];
        }

        try {
            // Find the phone number SID first
            $lookupResponse = $this->client()->get('/IncomingPhoneNumbers.json', [
                'PhoneNumber' => $phoneNumber,
            ]);

            if (! $lookupResponse->successful() || empty($lookupResponse->json()['incoming_phone_numbers'])) {
                return [
                    'success' => false,
                    'error' => 'Phone number not found in Twilio account',
                ];
            }

            $numberSid = $lookupResponse->json()['incoming_phone_numbers'][0]['sid'];

            // Update the CNAM
            $response = $this->client()->post("/IncomingPhoneNumbers/{$numberSid}.json", [
                'FriendlyName' => $callerName,
            ]);

            return [
                'success' => $response->successful(),
                'error' => $response->successful() ? null : 'Failed to update CNAM',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, string> $headers
     */
    public function verifyWebhook(string $payload, string $signature, array $headers = []): bool
    {
        if (empty($this->config['webhook_auth_token'])) {
            return true; // No verification configured
        }

        // Twilio uses X-Twilio-Signature header with HMAC-SHA1
        $url = $headers['X-Twilio-Url'] ?? '';
        $expectedSignature = base64_encode(
            hash_hmac('sha1', $url . $payload, $this->config['webhook_auth_token'], true)
        );

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get an HTTP client configured for Twilio API.
     */
    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        $accountSid = $this->config['account_sid'] ?? '';

        return Http::baseUrl("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}")
            ->withBasicAuth($accountSid, $this->config['auth_token'] ?? '')
            ->asForm()
            ->acceptJson()
            ->timeout(30);
    }

    /**
     * Mock a call for testing.
     *
     * @return array{success: bool, call_sid: string, status: string, provider: string}
     */
    private function mockCall(string $from, string $to, ?string $callReason): array
    {
        Log::info('Twilio MOCK call', compact('from', 'to', 'callReason'));

        return [
            'success' => true,
            'call_sid' => 'CA' . bin2hex(random_bytes(16)),
            'status' => 'queued',
            'provider' => 'twilio',
        ];
    }
}
