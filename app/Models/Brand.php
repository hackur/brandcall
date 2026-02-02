<?php

declare(strict_types=1);

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Brand Model - Represents a branded caller ID profile.
 *
 * A Brand contains the display information shown to call recipients,
 * including business name, logo, and call reason. Each brand can have
 * multiple associated phone numbers and tracks all call activity.
 *
 * @property int            $id              Primary key
 * @property int            $tenant_id       Foreign key to tenants table
 * @property string         $name            Internal brand name
 * @property string         $slug            URL-safe identifier for API routing
 * @property string|null    $display_name    CNAM display (max 32 chars)
 * @property string|null    $call_reason     Default call intent/reason
 * @property string|null    $logo_path       Path to uploaded logo file
 * @property string         $status          Brand status (draft|pending_vetting|active|suspended)
 * @property string|null    $numhub_brand_id External provider brand identifier
 * @property string         $api_key         Unique API key for this brand
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Tenant                              $tenant       The owning tenant
 * @property-read \Illuminate\Database\Eloquent\Collection<BrandPhoneNumber> $phoneNumbers Associated phone numbers
 * @property-read \Illuminate\Database\Eloquent\Collection<CallLog>          $callLogs     Call history
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Brand active()         Scope to active brands only
 * @method static \Illuminate\Database\Eloquent\Builder|Brand pendingVetting() Scope to brands awaiting approval
 */
class Brand extends Model
{
    use HasFactory;

    /**
     * Brand status constants.
     */
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING_VETTING = 'pending_vetting';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SUSPENDED = 'suspended';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'display_name',
        'call_reason',
        'logo_path',
        'status',
        'numhub_brand_id',
        'api_key',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'api_key',
        'numhub_brand_id', // Internal supplier reference - not exposed publicly
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Bootstrap the model and apply global scopes.
     *
     * Automatically applies TenantScope for multi-tenant isolation.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Get the tenant that owns this brand.
     *
     * @return BelongsTo<Tenant, Brand>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the phone numbers associated with this brand.
     *
     * @return HasMany<BrandPhoneNumber>
     */
    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(BrandPhoneNumber::class);
    }

    /**
     * Get the call logs for this brand.
     *
     * @return HasMany<CallLog>
     */
    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }

    /**
     * Scope a query to only include active brands.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Brand> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<Brand>
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include brands pending vetting.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Brand> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<Brand>
     */
    public function scopePendingVetting($query)
    {
        return $query->where('status', self::STATUS_PENDING_VETTING);
    }

    /**
     * Check if the brand is active and can make calls.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the brand is awaiting vetting approval.
     */
    public function isPendingVetting(): bool
    {
        return $this->status === self::STATUS_PENDING_VETTING;
    }

    /**
     * Submit the brand for vetting approval.
     *
     * @throws \InvalidArgumentException If brand is not in draft status
     */
    public function submitForVetting(): void
    {
        if ($this->status !== self::STATUS_DRAFT) {
            throw new \InvalidArgumentException(
                'Only draft brands can be submitted for vetting.'
            );
        }

        $this->update(['status' => self::STATUS_PENDING_VETTING]);
    }

    /**
     * Approve the brand after vetting.
     *
     * @throws \InvalidArgumentException If brand is not pending vetting
     */
    public function approve(): void
    {
        if ($this->status !== self::STATUS_PENDING_VETTING) {
            throw new \InvalidArgumentException(
                'Only brands pending vetting can be approved.'
            );
        }

        $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Suspend the brand (e.g., for compliance violations).
     */
    public function suspend(): void
    {
        $this->update(['status' => self::STATUS_SUSPENDED]);
    }

    /**
     * Generate a new API key for this brand.
     *
     * @return string The new API key (shown once)
     */
    public function regenerateApiKey(): string
    {
        $newKey = 'bci_' . bin2hex(random_bytes(32));
        $this->update(['api_key' => $newKey]);

        return $newKey;
    }

    /**
     * Get the full URL for the brand's logo.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        return asset('storage/' . $this->logo_path);
    }
}
