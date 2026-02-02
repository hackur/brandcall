<?php

/**
 * BrandCall - Branded Caller ID SaaS Platform.
 *
 * @author     BrandCall Development Team
 * @copyright  2024-2026 BrandCall
 * @license    Proprietary
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * WebhookLog Model - Logs incoming webhook requests.
 *
 * Records all incoming webhooks from voice providers (NumHub, Telnyx, Twilio)
 * for debugging, auditing, and replay purposes.
 *
 * @property int            $id         Primary key
 * @property string         $provider   Provider name (numhub, telnyx, twilio)
 * @property string         $event_type Event type from provider
 * @property array          $payload    Raw webhook payload (JSON)
 * @property array|null     $headers    Request headers (JSON)
 * @property string         $ip_address Source IP address
 * @property bool           $verified   Whether signature was verified
 * @property bool           $processed  Whether webhook was successfully processed
 * @property string|null    $error      Error message if processing failed
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WebhookLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider',
        'event_type',
        'payload',
        'headers',
        'ip_address',
        'verified',
        'processed',
        'error',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'headers' => 'array',
            'verified' => 'boolean',
            'processed' => 'boolean',
        ];
    }

    /**
     * Scope to only verified webhooks.
     *
     * @param \Illuminate\Database\Eloquent\Builder<WebhookLog> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<WebhookLog>
     */
    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    /**
     * Scope to only unprocessed webhooks (for retry queue).
     *
     * @param \Illuminate\Database\Eloquent\Builder<WebhookLog> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<WebhookLog>
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('processed', false);
    }

    /**
     * Mark the webhook as processed.
     */
    public function markProcessed(): void
    {
        $this->update(['processed' => true, 'error' => null]);
    }

    /**
     * Mark the webhook as failed with error.
     */
    public function markFailed(string $error): void
    {
        $this->update(['processed' => false, 'error' => $error]);
    }
}
