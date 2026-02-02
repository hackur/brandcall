<?php

/**
 * BrandCall - Branded Caller ID SaaS Platform.
 *
 * @author     BrandCall Development Team
 * @copyright  2024-2026 BrandCall
 * @license    Proprietary
 */

declare(strict_types=1);

namespace App\Services;

use App\Models\CallLog;
use App\Models\PricingTier;
use App\Models\Tenant;
use App\Models\UsageRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Usage Tracking Service - Manages call usage tracking and tiered pricing.
 *
 * This service is responsible for:
 * - Creating/updating monthly usage records per tenant
 * - Recording billable calls and updating counters
 * - Calculating tier-based pricing as volume increases
 * - Providing usage summaries for billing and dashboards
 * - Managing historical usage data for analytics
 *
 * Pricing Tiers (price per call):
 * - Starter (0-9,999): $0.075
 * - Growth (10,000-99,999): $0.065
 * - Scale (100,000-999,999): $0.050
 * - Enterprise (1,000,000-9,999,999): $0.035
 * - Enterprise+ (10,000,000+): $0.025
 *
 * Usage:
 * ```php
 * $service = app(UsageTrackingService::class);
 *
 * // Record a call
 * $service->recordCall($callLog);
 *
 * // Get usage summary
 * $summary = $service->getUsageSummary($tenant);
 * ```
 *
 * @see \App\Models\UsageRecord For the underlying data model
 * @see \App\Models\PricingTier For custom tenant pricing
 * @see \App\Jobs\SyncUsageToStripe For Stripe synchronization
 */
class UsageTrackingService
{
    /**
     * Get or create the current month's usage record for a tenant.
     *
     * Creates a new record if one doesn't exist for the current month.
     * Initializes all counters to zero and sets the initial tier price.
     *
     * @param Tenant $tenant The tenant to get/create usage record for
     *
     * @return UsageRecord The current month's usage record
     */
    public function getCurrentUsageRecord(Tenant $tenant): UsageRecord
    {
        $year = (int) now()->format('Y');
        $month = (int) now()->format('n');

        return UsageRecord::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'year' => $year,
                'month' => $month,
            ],
            [
                'call_count' => 0,
                'successful_calls' => 0,
                'failed_calls' => 0,
                'total_cost' => 0,
                'tier_price' => $this->getTierPrice(0),
                'synced_to_stripe' => false,
            ]
        );
    }

    /**
     * Record a call and update usage counters.
     *
     * Increments the appropriate counters (total, successful, or failed)
     * and recalculates the tier price based on new volume. Marks the
     * usage record as needing Stripe sync.
     *
     * Non-billable calls (flagged at creation) are ignored.
     *
     * @param CallLog $callLog The call log entry to record
     */
    public function recordCall(CallLog $callLog): void
    {
        if (! $callLog->billable) {
            return;
        }

        $usageRecord = $this->getCurrentUsageRecord($callLog->tenant);

        DB::transaction(function () use ($usageRecord, $callLog) {
            $usageRecord->increment('call_count');

            if ($callLog->status === 'completed') {
                $usageRecord->increment('successful_calls');
            } elseif ($callLog->status === 'failed') {
                $usageRecord->increment('failed_calls');
            }

            // Recalculate tier price based on new call count
            $newTierPrice = $this->getTierPrice($usageRecord->call_count);
            $usageRecord->tier_price = $newTierPrice;

            // Add call cost
            $usageRecord->total_cost += $callLog->cost ?? $newTierPrice;
            $usageRecord->synced_to_stripe = false;
            $usageRecord->save();
        });

        Log::info('Usage recorded', [
            'tenant_id' => $usageRecord->tenant_id,
            'call_count' => $usageRecord->call_count,
            'tier_price' => $usageRecord->tier_price,
        ]);
    }

    /**
     * Get the tier price for a given call count.
     *
     * First checks for custom tenant pricing tiers in the database.
     * Falls back to default pricing tiers if no custom tiers are set.
     *
     * Custom tiers allow enterprise clients to have negotiated pricing
     * that differs from the standard tier structure.
     *
     * @param int $callCount Current monthly call count
     *
     * @return float Price per call in dollars (e.g., 0.065)
     */
    public function getTierPrice(int $callCount): float
    {
        // Check database for custom tiers
        $tier = PricingTier::where('active', true)
            ->where('min_calls', '<=', $callCount)
            ->where(function ($query) use ($callCount) {
                $query->whereNull('max_calls')
                    ->orWhere('max_calls', '>=', $callCount);
            })
            ->orderBy('min_calls', 'desc')
            ->first();

        if ($tier) {
            return (float) $tier->price_per_call;
        }

        // Default tier pricing
        return match (true) {
            $callCount >= 10_000_000 => 0.025,
            $callCount >= 1_000_000 => 0.035,
            $callCount >= 100_000 => 0.050,
            $callCount >= 10_000 => 0.065,
            default => 0.075,
        };
    }

    /**
     * Get usage summary for a tenant for a specific month.
     *
     * Returns aggregated usage data including call counts, costs,
     * current tier information, and progress toward next tier.
     * Useful for dashboard displays and billing summaries.
     *
     * @param Tenant   $tenant The tenant to get usage for
     * @param int|null $year   Calendar year (defaults to current)
     * @param int|null $month  Calendar month 1-12 (defaults to current)
     *
     * @return array{
     *     call_count: int,
     *     successful_calls: int,
     *     failed_calls: int,
     *     total_cost: float,
     *     tier_price: float,
     *     tier_name: string,
     *     next_tier_at: int|null,
     *     savings_at_next_tier: float
     * }
     */
    public function getUsageSummary(Tenant $tenant, ?int $year = null, ?int $month = null): array
    {
        $year = $year ?? (int) now()->format('Y');
        $month = $month ?? (int) now()->format('n');

        $usageRecord = UsageRecord::where('tenant_id', $tenant->id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if (! $usageRecord) {
            return [
                'call_count' => 0,
                'successful_calls' => 0,
                'failed_calls' => 0,
                'total_cost' => 0,
                'tier_price' => $this->getTierPrice(0),
                'tier_name' => 'Starter',
                'next_tier_at' => 10_000,
                'savings_at_next_tier' => 0,
            ];
        }

        $tierInfo = $this->getTierInfo($usageRecord->call_count);

        return [
            'call_count' => $usageRecord->call_count,
            'successful_calls' => $usageRecord->successful_calls,
            'failed_calls' => $usageRecord->failed_calls,
            'total_cost' => $usageRecord->total_cost,
            'tier_price' => $usageRecord->tier_price,
            'tier_name' => $tierInfo['name'],
            'next_tier_at' => $tierInfo['next_tier_at'],
            'savings_at_next_tier' => $tierInfo['savings'],
        ];
    }

    /**
     * Get tier information for a call count.
     *
     * Determines the current tier and calculates progress toward
     * the next tier including potential savings at next tier.
     *
     * @param int $callCount Current monthly call count
     *
     * @return array{name: string, next_tier_at: int|null, savings: float}
     */
    private function getTierInfo(int $callCount): array
    {
        $tiers = [
            ['name' => 'Starter', 'min' => 0, 'max' => 9_999, 'price' => 0.075, 'next' => 10_000],
            ['name' => 'Growth', 'min' => 10_000, 'max' => 99_999, 'price' => 0.065, 'next' => 100_000],
            ['name' => 'Scale', 'min' => 100_000, 'max' => 999_999, 'price' => 0.050, 'next' => 1_000_000],
            ['name' => 'Enterprise', 'min' => 1_000_000, 'max' => 9_999_999, 'price' => 0.035, 'next' => 10_000_000],
            ['name' => 'Enterprise+', 'min' => 10_000_000, 'max' => null, 'price' => 0.025, 'next' => null],
        ];

        foreach ($tiers as $tier) {
            if ($callCount >= $tier['min'] && ($tier['max'] === null || $callCount <= $tier['max'])) {
                $nextTierAt = $tier['next'];
                $savings = 0;

                if ($nextTierAt !== null) {
                    $nextTierIndex = array_search($tier, $tiers) + 1;
                    if (isset($tiers[$nextTierIndex])) {
                        $nextTierPrice = $tiers[$nextTierIndex]['price'];
                        $savings = ($tier['price'] - $nextTierPrice) * $nextTierAt;
                    }
                }

                return [
                    'name' => $tier['name'],
                    'next_tier_at' => $nextTierAt,
                    'savings' => $savings,
                ];
            }
        }

        return ['name' => 'Starter', 'next_tier_at' => 10_000, 'savings' => 0];
    }

    /**
     * Get historical usage data for charts and analytics.
     *
     * Returns the last N months of usage data in chronological order,
     * suitable for rendering usage trend charts.
     *
     * @param Tenant $tenant The tenant to get history for
     * @param int    $months Number of months to retrieve (default 12)
     *
     * @return array<int, array{
     *     period: string,
     *     label: string,
     *     calls: int,
     *     cost: float,
     *     successful: int,
     *     failed: int
     * }>
     */
    public function getHistoricalUsage(Tenant $tenant, int $months = 12): array
    {
        $records = UsageRecord::where('tenant_id', $tenant->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit($months)
            ->get()
            ->reverse()
            ->values();

        return $records->map(fn ($record) => [
            'period' => sprintf('%d-%02d', $record->year, $record->month),
            'label' => now()->setYear($record->year)->setMonth($record->month)->format('M Y'),
            'calls' => $record->call_count,
            'cost' => $record->total_cost,
            'successful' => $record->successful_calls,
            'failed' => $record->failed_calls,
        ])->toArray();
    }
}
