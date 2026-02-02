<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;

/**
 * Tenant Model - Represents a customer organization.
 *
 * Tenants are the top-level entity for multi-tenancy. Each tenant
 * can have multiple users, brands, and tracks their own usage and billing.
 * All tenant-scoped models are automatically filtered by the TenantScope.
 *
 * @property int                 $id                           Primary key
 * @property string              $name                         Company/organization name
 * @property string              $email                        Primary contact email
 * @property string              $slug                         URL-safe identifier for routing
 * @property string|null         $stripe_customer_id           Stripe customer ID for billing
 * @property string              $subscription_tier            Current tier (starter|growth|enterprise)
 * @property int|null            $monthly_call_limit           Max calls per month (null = unlimited)
 * @property bool                $analytics_monitoring_enabled Advanced analytics enabled
 * @property array|null          $settings                     Custom tenant settings (JSON)
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property \Carbon\Carbon|null $deleted_at                   Soft delete timestamp
 * @property-read \Illuminate\Database\Eloquent\Collection<User>        $users        Tenant members
 * @property-read \Illuminate\Database\Eloquent\Collection<Brand>       $brands       Branded caller IDs
 * @property-read \Illuminate\Database\Eloquent\Collection<CallLog>     $callLogs     All call activity
 * @property-read \Illuminate\Database\Eloquent\Collection<UsageRecord> $usageRecords Monthly usage records
 */
class Tenant extends Model
{
    use Billable;
    use HasFactory;
    use SoftDeletes;

    /**
     * Subscription tier constants.
     */
    public const TIER_STARTER = 'starter';

    public const TIER_GROWTH = 'growth';

    public const TIER_ENTERPRISE = 'enterprise';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'slug',
        'phone',
        'website',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
        'subscription_tier',
        'monthly_call_limit',
        'analytics_monitoring_enabled',
        'settings',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'analytics_monitoring_enabled' => 'boolean',
            'monthly_call_limit' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the users belonging to this tenant.
     *
     * @return HasMany<User>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the brands owned by this tenant.
     *
     * Note: This bypasses TenantScope since we're already on the tenant.
     *
     * @return HasMany<Brand>
     */
    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    /**
     * Get all call logs for this tenant.
     *
     * @return HasMany<CallLog>
     */
    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }

    /**
     * Get monthly usage records for this tenant.
     *
     * @return HasMany<UsageRecord>
     */
    public function usageRecords(): HasMany
    {
        return $this->hasMany(UsageRecord::class);
    }

    /**
     * Get the owner user (first user with owner role).
     */
    public function getOwnerAttribute(): ?User
    {
        return $this->users()
            ->whereHas('roles', fn ($q) => $q->where('name', 'owner'))
            ->first();
    }

    /**
     * Check if tenant has reached their monthly call limit.
     */
    public function hasReachedCallLimit(): bool
    {
        if ($this->monthly_call_limit === null) {
            return false; // Unlimited
        }

        $currentMonthCalls = $this->callLogs()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return $currentMonthCalls >= $this->monthly_call_limit;
    }

    /**
     * Get the current month's usage record.
     */
    public function getCurrentUsageRecord(): ?UsageRecord
    {
        return $this->usageRecords()
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();
    }

    /**
     * Check if tenant is on enterprise tier.
     */
    public function isEnterprise(): bool
    {
        return $this->subscription_tier === self::TIER_ENTERPRISE;
    }

    /**
     * Get the price per call based on volume tier.
     *
     * @param int $monthlyVolume Current month's call count
     */
    public function getPricePerCall(int $monthlyVolume): float
    {
        // Get pricing tier based on volume
        $tier = PricingTier::where('min_calls', '<=', $monthlyVolume)
            ->where(function ($q) use ($monthlyVolume) {
                $q->whereNull('max_calls')
                    ->orWhere('max_calls', '>=', $monthlyVolume);
            })
            ->first();

        return $tier?->price_per_call ?? 0.075;
    }
}
