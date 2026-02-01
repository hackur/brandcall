<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use App\Models\UsageRecord;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\UsageRecord as StripeUsageRecord;

/**
 * Stripe Billing Service.
 *
 * Handles customer creation, subscriptions, and usage-based billing.
 */
class StripeService
{
    private bool $enabled;

    public function __construct()
    {
        $this->enabled = ! empty(config('services.stripe.secret'));

        if ($this->enabled) {
            Stripe::setApiKey(config('services.stripe.secret'));
        }
    }

    /**
     * Check if Stripe is configured.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Create or get a Stripe customer for a tenant.
     */
    public function getOrCreateCustomer(Tenant $tenant): ?Customer
    {
        if (! $this->enabled) {
            Log::info('Stripe disabled, skipping customer creation');

            return null;
        }

        try {
            // Return existing customer
            if ($tenant->stripe_customer_id) {
                return Customer::retrieve($tenant->stripe_customer_id);
            }

            // Create new customer
            $customer = Customer::create([
                'email' => $tenant->email,
                'name' => $tenant->name,
                'metadata' => [
                    'tenant_id' => $tenant->id,
                    'tenant_slug' => $tenant->slug,
                ],
            ]);

            // Save customer ID to tenant
            $tenant->update(['stripe_customer_id' => $customer->id]);

            Log::info('Stripe customer created', [
                'tenant_id' => $tenant->id,
                'customer_id' => $customer->id,
            ]);

            return $customer;
        } catch (ApiErrorException $e) {
            Log::error('Stripe customer creation failed', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Create a metered subscription for a tenant.
     */
    public function createSubscription(Tenant $tenant): ?Subscription
    {
        if (! $this->enabled) {
            return null;
        }

        $priceId = config('services.stripe.metered_price_id');

        if (! $priceId) {
            Log::warning('Stripe metered price ID not configured');

            return null;
        }

        try {
            $customer = $this->getOrCreateCustomer($tenant);

            if (! $customer) {
                return null;
            }

            // Check for existing subscription
            if ($tenant->stripe_subscription_id) {
                return Subscription::retrieve($tenant->stripe_subscription_id);
            }

            // Create metered subscription
            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [
                    ['price' => $priceId],
                ],
                'metadata' => [
                    'tenant_id' => $tenant->id,
                ],
            ]);

            // Save subscription ID
            $tenant->update([
                'stripe_subscription_id' => $subscription->id,
                'stripe_subscription_item_id' => $subscription->items->data[0]->id,
            ]);

            Log::info('Stripe subscription created', [
                'tenant_id' => $tenant->id,
                'subscription_id' => $subscription->id,
            ]);

            return $subscription;
        } catch (ApiErrorException $e) {
            Log::error('Stripe subscription creation failed', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Report usage to Stripe.
     */
    public function reportUsage(Tenant $tenant, int $quantity, ?int $timestamp = null): ?StripeUsageRecord
    {
        if (! $this->enabled) {
            Log::info('Stripe disabled, skipping usage report', [
                'tenant_id' => $tenant->id,
                'quantity' => $quantity,
            ]);

            return null;
        }

        $subscriptionItemId = $tenant->stripe_subscription_item_id;

        if (! $subscriptionItemId) {
            Log::warning('No subscription item ID for tenant', ['tenant_id' => $tenant->id]);

            return null;
        }

        try {
            $usageRecord = StripeUsageRecord::createOnSubscriptionItem(
                $subscriptionItemId,
                [
                    'quantity' => $quantity,
                    'timestamp' => $timestamp ?? time(),
                    'action' => 'increment',
                ]
            );

            Log::info('Stripe usage reported', [
                'tenant_id' => $tenant->id,
                'quantity' => $quantity,
                'usage_record_id' => $usageRecord->id,
            ]);

            return $usageRecord;
        } catch (ApiErrorException $e) {
            Log::error('Stripe usage report failed', [
                'tenant_id' => $tenant->id,
                'quantity' => $quantity,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Sync all unsync'd usage records to Stripe.
     */
    public function syncPendingUsage(): int
    {
        if (! $this->enabled) {
            return 0;
        }

        $pendingRecords = UsageRecord::where('synced_to_stripe', false)
            ->where('call_count', '>', 0)
            ->with('tenant')
            ->get();

        $synced = 0;

        foreach ($pendingRecords as $record) {
            $tenant = $record->tenant;

            if (! $tenant || ! $tenant->stripe_subscription_item_id) {
                continue;
            }

            // Calculate calls since last sync
            $callsToReport = $record->call_count - ($record->last_synced_count ?? 0);

            if ($callsToReport <= 0) {
                continue;
            }

            $result = $this->reportUsage($tenant, $callsToReport);

            if ($result) {
                $record->update([
                    'synced_to_stripe' => true,
                    'stripe_usage_record_id' => $result->id,
                    'last_synced_count' => $record->call_count,
                    'last_synced_at' => now(),
                ]);
                $synced++;
            }
        }

        Log::info('Stripe usage sync completed', ['synced' => $synced]);

        return $synced;
    }

    /**
     * Get subscription status for a tenant.
     */
    public function getSubscriptionStatus(Tenant $tenant): array
    {
        if (! $this->enabled || ! $tenant->stripe_subscription_id) {
            return [
                'active' => false,
                'status' => 'none',
                'current_period_end' => null,
            ];
        }

        try {
            $subscription = Subscription::retrieve($tenant->stripe_subscription_id);

            return [
                'active' => $subscription->status === 'active',
                'status' => $subscription->status,
                'current_period_end' => $subscription->current_period_end,
                'cancel_at_period_end' => $subscription->cancel_at_period_end,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Failed to get subscription status', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'active' => false,
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(Tenant $tenant, bool $atPeriodEnd = true): bool
    {
        if (! $this->enabled || ! $tenant->stripe_subscription_id) {
            return false;
        }

        try {
            $subscription = Subscription::retrieve($tenant->stripe_subscription_id);

            if ($atPeriodEnd) {
                $subscription->cancel_at_period_end = true;
                $subscription->save();
            } else {
                $subscription->cancel();
            }

            Log::info('Subscription cancelled', [
                'tenant_id' => $tenant->id,
                'at_period_end' => $atPeriodEnd,
            ]);

            return true;
        } catch (ApiErrorException $e) {
            Log::error('Subscription cancellation failed', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
