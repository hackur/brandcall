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
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * UsageRecord Model - Monthly usage aggregation for billing.
 *
 * Tracks aggregated call counts and costs per tenant per month.
 * Used for:
 * - Usage-based billing calculations
 * - Tier-based pricing determination
 * - Stripe meter synchronization
 * - Usage analytics and reporting
 *
 * Records are created/updated by UsageTrackingService when calls
 * are made. Synced to Stripe for metered billing.
 *
 * Pricing Tiers (per call):
 * - 0-9,999 calls: $0.075
 * - 10,000-99,999 calls: $0.065
 * - 100,000-999,999 calls: $0.050
 * - 1,000,000-9,999,999 calls: $0.035
 * - 10,000,000+ calls: $0.025
 *
 * @property int            $id                     Primary key
 * @property int            $tenant_id              Owning tenant
 * @property int            $year                   Calendar year (e.g., 2026)
 * @property int            $month                  Calendar month (1-12)
 * @property int            $call_count             Total calls this period
 * @property int            $successful_calls       Completed calls
 * @property int            $failed_calls           Failed/unanswered calls
 * @property int            $spam_flagged_calls     Calls flagged as spam
 * @property float          $total_cost             Total cost for period
 * @property float          $tier_price             Current tier price per call
 * @property string|null    $stripe_usage_record_id Stripe meter record ID
 * @property bool           $synced_to_stripe       Whether synced to Stripe
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Tenant $tenant Owning tenant
 */
class UsageRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'year',
        'month',
        'call_count',
        'successful_calls',
        'failed_calls',
        'spam_flagged_calls',
        'total_cost',
        'tier_price',
        'stripe_usage_record_id',
        'synced_to_stripe',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'call_count' => 'integer',
            'successful_calls' => 'integer',
            'failed_calls' => 'integer',
            'spam_flagged_calls' => 'integer',
            'total_cost' => 'decimal:2',
            'tier_price' => 'decimal:4',
            'synced_to_stripe' => 'boolean',
        ];
    }

    /**
     * Get the tenant that owns this usage record.
     *
     * @return BelongsTo<Tenant, UsageRecord>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the period label (e.g., "January 2026").
     *
     * @return string Human-readable period label
     */
    public function getPeriodLabel(): string
    {
        return now()->setYear($this->year)->setMonth($this->month)->format('F Y');
    }

    /**
     * Get the success rate as a percentage.
     *
     * @return float Success rate (0-100)
     */
    public function getSuccessRate(): float
    {
        if ($this->call_count === 0) {
            return 0;
        }

        return round(($this->successful_calls / $this->call_count) * 100, 2);
    }

    /**
     * Get the spam rate as a percentage.
     *
     * @return float Spam rate (0-100)
     */
    public function getSpamRate(): float
    {
        if ($this->call_count === 0) {
            return 0;
        }

        return round(($this->spam_flagged_calls / $this->call_count) * 100, 2);
    }

    /**
     * Check if this record has been synced to Stripe.
     *
     * @return bool True if synced
     */
    public function isSynced(): bool
    {
        return $this->synced_to_stripe;
    }

    /**
     * Mark the record as synced to Stripe.
     *
     * @param string $stripeRecordId The Stripe usage record ID
     */
    public function markSynced(string $stripeRecordId): void
    {
        $this->update([
            'stripe_usage_record_id' => $stripeRecordId,
            'synced_to_stripe' => true,
        ]);
    }
}
