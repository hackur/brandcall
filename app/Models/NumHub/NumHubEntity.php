<?php

declare(strict_types=1);

namespace App\Models\NumHub;

use App\Models\Brand;
use App\Models\Tenant;
use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * NumHubEntity Model - BCID application mapping.
 *
 * Maps BrandCall tenants/brands to NumHub BCID applications.
 * Each entity represents a submitted BCID application and tracks
 * its vetting status through the NumHub approval process.
 *
 * Status Flow:
 * draft → submitted → pending_vetting → approved
 *                                     → rejected
 *
 * @property string              $id                UUID primary key
 * @property int                 $tenant_id         Owning tenant
 * @property int|null            $brand_id          Associated brand (optional)
 * @property string              $numhub_entity_id  NumhubEntityId from API
 * @property string|null         $numhub_eid        Enterprise ID (EID)
 * @property string              $entity_type       Type: enterprise|bpo|direct
 * @property string              $company_name      Business name submitted
 * @property string              $status            Internal status tracking
 * @property string              $vetting_status    NumHub vetting state
 * @property string|null         $attestation_level STIR/SHAKEN level (A/B/C)
 * @property array|null          $api_response      Full API response cache
 * @property \Carbon\Carbon|null $submitted_at      When submitted to NumHub
 * @property \Carbon\Carbon|null $approved_at       When approved by NumHub
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property-read Tenant         $tenant             Owning tenant
 * @property-read Brand|null     $brand              Associated brand
 * @property-read \Illuminate\Database\Eloquent\Collection<NumHubIdentity> $identities
 * @property-read \Illuminate\Database\Eloquent\Collection<NumHubDocument> $documents
 * @property-read \Illuminate\Database\Eloquent\Collection<NumHubSyncLog>  $syncLogs
 */
class NumHubEntity extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'numhub_entities';

    /**
     * Entity type constants.
     */
    public const TYPE_ENTERPRISE = 'enterprise';

    public const TYPE_BPO = 'bpo';

    public const TYPE_DIRECT = 'direct';

    /**
     * Status constants (internal tracking).
     */
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_PENDING_VETTING = 'pending_vetting';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /**
     * Vetting status constants (from NumHub).
     */
    public const VETTING_PENDING = 'pending';

    public const VETTING_IN_REVIEW = 'in_review';

    public const VETTING_APPROVED = 'approved';

    public const VETTING_REJECTED = 'rejected';

    /**
     * Attestation level constants.
     */
    public const ATTESTATION_A = 'A';

    public const ATTESTATION_B = 'B';

    public const ATTESTATION_C = 'C';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'brand_id',
        'numhub_entity_id',
        'numhub_eid',
        'entity_type',
        'company_name',
        'status',
        'vetting_status',
        'attestation_level',
        'api_response',
        'submitted_at',
        'approved_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'api_response' => 'array',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Bootstrap the model and apply global scopes.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Get the tenant that owns this entity.
     *
     * @return BelongsTo<Tenant, NumHubEntity>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the brand associated with this entity.
     *
     * @return BelongsTo<Brand, NumHubEntity>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the identities for this entity.
     *
     * @return HasMany<NumHubIdentity>
     */
    public function identities(): HasMany
    {
        return $this->hasMany(NumHubIdentity::class, 'numhub_entity_id');
    }

    /**
     * Get the documents for this entity.
     *
     * @return HasMany<NumHubDocument>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(NumHubDocument::class, 'numhub_entity_id');
    }

    /**
     * Get the sync logs for this entity.
     *
     * @return HasMany<NumHubSyncLog>
     */
    public function syncLogs(): HasMany
    {
        return $this->hasMany(NumHubSyncLog::class, 'numhub_entity_id');
    }

    /**
     * Check if the entity is approved.
     *
     * @return bool True if approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED
            || $this->vetting_status === self::VETTING_APPROVED;
    }

    /**
     * Check if the entity is pending vetting.
     *
     * @return bool True if pending
     */
    public function isPending(): bool
    {
        return in_array($this->status, [
            self::STATUS_SUBMITTED,
            self::STATUS_PENDING_VETTING,
        ], true);
    }

    /**
     * Check if the entity was rejected.
     *
     * @return bool True if rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED
            || $this->vetting_status === self::VETTING_REJECTED;
    }

    /**
     * Mark as submitted to NumHub.
     */
    public function markAsSubmitted(): void
    {
        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    /**
     * Mark as approved by NumHub.
     */
    public function markAsApproved(): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'vetting_status' => self::VETTING_APPROVED,
            'approved_at' => now(),
        ]);
    }

    /**
     * Update status from NumHub API response.
     *
     * @param array<string, mixed> $response NumHub API response
     */
    public function updateFromApiResponse(array $response): void
    {
        $this->update([
            'api_response' => $response,
            'vetting_status' => $response['vettingStatus'] ?? $this->vetting_status,
            'attestation_level' => $response['attestationLevel'] ?? $this->attestation_level,
            'numhub_eid' => $response['eid'] ?? $this->numhub_eid,
        ]);
    }

    /**
     * Find by NumHub entity ID.
     *
     * @param string $numhubEntityId The NumhubEntityId from API
     *
     * @return self|null The entity or null
     */
    public static function findByNumHubId(string $numhubEntityId): ?self
    {
        return static::withoutGlobalScope(TenantScope::class)
            ->where('numhub_entity_id', $numhubEntityId)
            ->first();
    }

    /**
     * Scope to entities pending vetting.
     *
     * @param \Illuminate\Database\Eloquent\Builder<NumHubEntity> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<NumHubEntity>
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', [
            self::STATUS_SUBMITTED,
            self::STATUS_PENDING_VETTING,
        ]);
    }

    /**
     * Scope to approved entities.
     *
     * @param \Illuminate\Database\Eloquent\Builder<NumHubEntity> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<NumHubEntity>
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Get all entity types with labels.
     *
     * @return array<string, string>
     */
    public static function getEntityTypes(): array
    {
        return [
            self::TYPE_ENTERPRISE => 'Enterprise',
            self::TYPE_BPO => 'BPO / Call Center',
            self::TYPE_DIRECT => 'Direct',
        ];
    }

    /**
     * Get all attestation levels with labels.
     *
     * @return array<string, string>
     */
    public static function getAttestationLevels(): array
    {
        return [
            self::ATTESTATION_A => 'A - Full Attestation',
            self::ATTESTATION_B => 'B - Partial Attestation',
            self::ATTESTATION_C => 'C - Gateway Attestation',
        ];
    }
}
