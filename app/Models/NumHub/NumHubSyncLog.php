<?php

declare(strict_types=1);

namespace App\Models\NumHub;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * NumHubSyncLog Model - API audit trail.
 *
 * Records all API interactions with NumHub for debugging and compliance.
 * Logs are immutable (no updated_at) and should be cleaned up periodically.
 *
 * Recommended retention: 90 days for production environments.
 *
 * @property string         $id                 UUID primary key
 * @property int|null       $tenant_id          Associated tenant
 * @property string|null    $numhub_entity_id   Related entity UUID
 * @property string|null    $numhub_identity_id Related identity UUID
 * @property string         $endpoint           API endpoint path
 * @property string         $method             HTTP method
 * @property string         $direction          Direction: outbound|inbound
 * @property int|null       $status_code        HTTP response code
 * @property array|null     $request_payload    Request data (redacted)
 * @property array|null     $response_payload   Response data
 * @property int|null       $response_time_ms   API response time in ms
 * @property string|null    $error_message      Error details if failed
 * @property bool           $success            Quick filter flag
 * @property \Carbon\Carbon $created_at
 * @property-read Tenant|null    $tenant              Associated tenant
 * @property-read NumHubEntity|null $entity           Related entity
 * @property-read NumHubIdentity|null $identity       Related identity
 */
class NumHubSyncLog extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'numhub_sync_logs';

    /**
     * Indicates if the model should be timestamped.
     * We only use created_at since logs are immutable.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Direction constants.
     */
    public const DIRECTION_OUTBOUND = 'outbound';

    public const DIRECTION_INBOUND = 'inbound';

    /**
     * HTTP method constants.
     */
    public const METHOD_GET = 'GET';

    public const METHOD_POST = 'POST';

    public const METHOD_PUT = 'PUT';

    public const METHOD_DELETE = 'DELETE';

    /**
     * Sensitive fields to redact from payloads.
     *
     * @var array<int, string>
     */
    protected static array $sensitiveFields = [
        'password',
        'access_token',
        'refresh_token',
        'api_key',
        'api_secret',
        'authorization',
        'ssn',
        'tax_id',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'numhub_entity_id',
        'numhub_identity_id',
        'endpoint',
        'method',
        'direction',
        'status_code',
        'request_payload',
        'response_payload',
        'response_time_ms',
        'error_message',
        'success',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'response_payload' => 'array',
            'status_code' => 'integer',
            'response_time_ms' => 'integer',
            'success' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the tenant associated with this log.
     *
     * @return BelongsTo<Tenant, NumHubSyncLog>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the entity associated with this log.
     *
     * @return BelongsTo<NumHubEntity, NumHubSyncLog>
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(NumHubEntity::class, 'numhub_entity_id');
    }

    /**
     * Get the identity associated with this log.
     *
     * @return BelongsTo<NumHubIdentity, NumHubSyncLog>
     */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(NumHubIdentity::class, 'numhub_identity_id');
    }

    /**
     * Check if this was a successful API call.
     *
     * @return bool True if successful
     */
    public function isSuccess(): bool
    {
        return $this->success === true;
    }

    /**
     * Check if this was an outbound API call.
     *
     * @return bool True if outbound
     */
    public function isOutbound(): bool
    {
        return $this->direction === self::DIRECTION_OUTBOUND;
    }

    /**
     * Check if this was an inbound webhook.
     *
     * @return bool True if inbound
     */
    public function isInbound(): bool
    {
        return $this->direction === self::DIRECTION_INBOUND;
    }

    /**
     * Log an outbound API request.
     *
     * @param array{
     *     tenant_id?: int|null,
     *     numhub_entity_id?: string|null,
     *     numhub_identity_id?: string|null,
     *     endpoint: string,
     *     method: string,
     *     request_payload?: array<string, mixed>|null,
     *     response_payload?: array<string, mixed>|null,
     *     status_code?: int|null,
     *     response_time_ms?: int|null,
     *     error_message?: string|null,
     *     success: bool
     * } $data Log data
     *
     * @return self The created log
     */
    public static function logOutbound(array $data): self
    {
        return static::create([
            'tenant_id' => $data['tenant_id'] ?? null,
            'numhub_entity_id' => $data['numhub_entity_id'] ?? null,
            'numhub_identity_id' => $data['numhub_identity_id'] ?? null,
            'endpoint' => $data['endpoint'],
            'method' => $data['method'],
            'direction' => self::DIRECTION_OUTBOUND,
            'request_payload' => self::redactSensitive($data['request_payload'] ?? null),
            'response_payload' => self::redactSensitive($data['response_payload'] ?? null),
            'status_code' => $data['status_code'] ?? null,
            'response_time_ms' => $data['response_time_ms'] ?? null,
            'error_message' => $data['error_message'] ?? null,
            'success' => $data['success'],
            'created_at' => now(),
        ]);
    }

    /**
     * Log an inbound webhook.
     *
     * @param array{
     *     tenant_id?: int|null,
     *     numhub_entity_id?: string|null,
     *     numhub_identity_id?: string|null,
     *     endpoint: string,
     *     request_payload?: array<string, mixed>|null,
     *     success: bool,
     *     error_message?: string|null
     * } $data Webhook data
     *
     * @return self The created log
     */
    public static function logInbound(array $data): self
    {
        return static::create([
            'tenant_id' => $data['tenant_id'] ?? null,
            'numhub_entity_id' => $data['numhub_entity_id'] ?? null,
            'numhub_identity_id' => $data['numhub_identity_id'] ?? null,
            'endpoint' => $data['endpoint'],
            'method' => self::METHOD_POST,
            'direction' => self::DIRECTION_INBOUND,
            'request_payload' => self::redactSensitive($data['request_payload'] ?? null),
            'success' => $data['success'],
            'error_message' => $data['error_message'] ?? null,
            'created_at' => now(),
        ]);
    }

    /**
     * Redact sensitive fields from payload.
     *
     * @param array<string, mixed>|null $payload Original payload
     *
     * @return array<string, mixed>|null Redacted payload
     */
    public static function redactSensitive(?array $payload): ?array
    {
        if ($payload === null) {
            return null;
        }

        return self::redactRecursive($payload);
    }

    /**
     * Recursively redact sensitive fields.
     *
     * @param array<string, mixed> $data Data to redact
     *
     * @return array<string, mixed> Redacted data
     */
    protected static function redactRecursive(array $data): array
    {
        foreach ($data as $key => $value) {
            $lowerKey = strtolower((string) $key);

            // Check if this is a sensitive field
            foreach (self::$sensitiveFields as $sensitive) {
                if (str_contains($lowerKey, $sensitive)) {
                    $data[$key] = '[REDACTED]';

                    continue 2;
                }
            }

            // Recursively process arrays
            if (is_array($value)) {
                $data[$key] = self::redactRecursive($value);
            }
        }

        return $data;
    }

    /**
     * Delete logs older than specified days.
     *
     * @param int $days Number of days to retain
     *
     * @return int Number of deleted records
     */
    public static function deleteOlderThan(int $days): int
    {
        return static::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Scope to successful calls.
     *
     * @param \Illuminate\Database\Eloquent\Builder<NumHubSyncLog> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<NumHubSyncLog>
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    /**
     * Scope to failed calls.
     *
     * @param \Illuminate\Database\Eloquent\Builder<NumHubSyncLog> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<NumHubSyncLog>
     */
    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    /**
     * Scope to outbound API calls.
     *
     * @param \Illuminate\Database\Eloquent\Builder<NumHubSyncLog> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<NumHubSyncLog>
     */
    public function scopeOutbound($query)
    {
        return $query->where('direction', self::DIRECTION_OUTBOUND);
    }

    /**
     * Scope to inbound webhooks.
     *
     * @param \Illuminate\Database\Eloquent\Builder<NumHubSyncLog> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<NumHubSyncLog>
     */
    public function scopeInbound($query)
    {
        return $query->where('direction', self::DIRECTION_INBOUND);
    }

    /**
     * Scope to specific endpoint.
     *
     * @param \Illuminate\Database\Eloquent\Builder<NumHubSyncLog> $query
     * @param string                                               $endpoint Endpoint to filter
     *
     * @return \Illuminate\Database\Eloquent\Builder<NumHubSyncLog>
     */
    public function scopeForEndpoint($query, string $endpoint)
    {
        return $query->where('endpoint', 'like', "%{$endpoint}%");
    }
}
