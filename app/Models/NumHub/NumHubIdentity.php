<?php

declare(strict_types=1);

namespace App\Models\NumHub;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * NumHubIdentity Model - Display identity mapping.
 *
 * Tracks caller ID display configurations (NumhubIdentityId).
 * Each identity belongs to an entity and represents a specific
 * CNAM display name, logo, and call reason combination.
 *
 * @property string         $id                 UUID primary key
 * @property string         $numhub_entity_id   Parent entity UUID
 * @property string         $numhub_identity_id NumhubIdentityId from API
 * @property string         $display_name       CNAM display (max 32 chars)
 * @property string|null    $call_reason        Default call reason
 * @property string|null    $logo_url           Logo URL in NumHub
 * @property string         $status             Status: active|inactive|pending
 * @property array|null     $rich_call_data     Full RCD configuration
 * @property array|null     $phone_numbers      Array of associated TNs
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read NumHubEntity   $entity              Parent entity
 * @property-read \Illuminate\Database\Eloquent\Collection<NumHubSyncLog> $syncLogs
 */
class NumHubIdentity extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'numhub_identities';

    /**
     * Status constants.
     */
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_PENDING = 'pending';

    /**
     * Maximum display name length per CNAM spec.
     */
    public const MAX_DISPLAY_NAME_LENGTH = 32;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numhub_entity_id',
        'numhub_identity_id',
        'display_name',
        'call_reason',
        'logo_url',
        'status',
        'rich_call_data',
        'phone_numbers',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rich_call_data' => 'array',
            'phone_numbers' => 'array',
        ];
    }

    /**
     * Get the entity that owns this identity.
     *
     * @return BelongsTo<NumHubEntity, NumHubIdentity>
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(NumHubEntity::class, 'numhub_entity_id');
    }

    /**
     * Get the sync logs for this identity.
     *
     * @return HasMany<NumHubSyncLog>
     */
    public function syncLogs(): HasMany
    {
        return $this->hasMany(NumHubSyncLog::class, 'numhub_identity_id');
    }

    /**
     * Check if the identity is active.
     *
     * @return bool True if active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the identity is pending activation.
     *
     * @return bool True if pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Get the count of associated phone numbers.
     *
     * @return int Number of phone numbers
     */
    public function getPhoneNumberCount(): int
    {
        return is_array($this->phone_numbers) ? count($this->phone_numbers) : 0;
    }

    /**
     * Add phone numbers to this identity.
     *
     * @param array<int, string> $numbers Phone numbers to add
     */
    public function addPhoneNumbers(array $numbers): void
    {
        $existing = $this->phone_numbers ?? [];
        $merged = array_unique(array_merge($existing, $numbers));

        $this->update(['phone_numbers' => array_values($merged)]);
    }

    /**
     * Remove phone numbers from this identity.
     *
     * @param array<int, string> $numbers Phone numbers to remove
     */
    public function removePhoneNumbers(array $numbers): void
    {
        $existing = $this->phone_numbers ?? [];
        $filtered = array_diff($existing, $numbers);

        $this->update(['phone_numbers' => array_values($filtered)]);
    }

    /**
     * Check if a phone number is associated with this identity.
     *
     * @param string $number Phone number to check
     *
     * @return bool True if associated
     */
    public function hasPhoneNumber(string $number): bool
    {
        return in_array($number, $this->phone_numbers ?? [], true);
    }

    /**
     * Update from NumHub API response.
     *
     * @param array<string, mixed> $response API response
     */
    public function updateFromApiResponse(array $response): void
    {
        $this->update([
            'display_name' => $response['displayName'] ?? $this->display_name,
            'call_reason' => $response['callReason'] ?? $this->call_reason,
            'logo_url' => $response['logoUrl'] ?? $this->logo_url,
            'status' => $this->mapApiStatus($response['status'] ?? null),
            'rich_call_data' => $response['richCallData'] ?? $this->rich_call_data,
            'phone_numbers' => $response['phoneNumbers'] ?? $this->phone_numbers,
        ]);
    }

    /**
     * Map NumHub API status to internal status.
     *
     * @param string|null $apiStatus Status from API
     *
     * @return string Internal status
     */
    protected function mapApiStatus(?string $apiStatus): string
    {
        return match (strtolower($apiStatus ?? '')) {
            'active', 'approved' => self::STATUS_ACTIVE,
            'inactive', 'deactivated' => self::STATUS_INACTIVE,
            default => self::STATUS_PENDING,
        };
    }

    /**
     * Find by NumHub identity ID.
     *
     * @param string $numhubIdentityId The NumhubIdentityId from API
     *
     * @return self|null The identity or null
     */
    public static function findByNumHubId(string $numhubIdentityId): ?self
    {
        return static::where('numhub_identity_id', $numhubIdentityId)->first();
    }

    /**
     * Scope to active identities.
     *
     * @param \Illuminate\Database\Eloquent\Builder<NumHubIdentity> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<NumHubIdentity>
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to pending identities.
     *
     * @param \Illuminate\Database\Eloquent\Builder<NumHubIdentity> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<NumHubIdentity>
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
