<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\StripeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Sync Usage to Stripe Job.
 *
 * Syncs all pending usage records to Stripe for metered billing.
 * Should be scheduled to run hourly via Laravel scheduler.
 */
class SyncUsageToStripe implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public int $backoff = 60;

    /**
     * Execute the job.
     */
    public function handle(StripeService $stripeService): void
    {
        Log::info('Starting Stripe usage sync job');

        $synced = $stripeService->syncPendingUsage();

        Log::info('Stripe usage sync completed', ['synced_count' => $synced]);
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Stripe usage sync job failed', [
            'error' => $exception->getMessage(),
        ]);
    }
}
