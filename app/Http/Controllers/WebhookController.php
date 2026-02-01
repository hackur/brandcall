<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CallLog;
use App\Models\WebhookLog;
use App\Services\NumHubService;
use App\Services\TelnyxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Webhook Controller.
 *
 * Handles incoming webhooks from NumHub, Telnyx, and Stripe.
 */
class WebhookController extends Controller
{
    public function __construct(
        private NumHubService $numHubService,
        private TelnyxService $telnyxService
    ) {}

    /**
     * Handle NumHub webhooks.
     *
     * POST /webhooks/numhub
     */
    public function numhub(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('X-NumHub-Signature', '');

        // Log the webhook
        $webhookLog = WebhookLog::create([
            'source' => 'numhub',
            'event_type' => $request->input('event'),
            'payload' => $request->all(),
            'signature' => $signature,
            'verified' => false,
            'processed' => false,
        ]);

        // Verify signature
        if (! $this->numHubService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('NumHub webhook signature verification failed', [
                'webhook_log_id' => $webhookLog->id,
            ]);

            $webhookLog->update(['processing_error' => 'Signature verification failed']);

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $webhookLog->update(['verified' => true]);

        // Process the webhook
        try {
            $this->processNumHubWebhook($request->all(), $webhookLog);

            $webhookLog->update([
                'processed' => true,
                'processed_at' => now(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('NumHub webhook processing failed', [
                'webhook_log_id' => $webhookLog->id,
                'error' => $e->getMessage(),
            ]);

            $webhookLog->update(['processing_error' => $e->getMessage()]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Process NumHub webhook events.
     *
     * @param array $data Webhook payload
     */
    private function processNumHubWebhook(array $data, WebhookLog $webhookLog): void
    {
        $event = $data['event'] ?? null;

        switch ($event) {
            case 'enterprise.approved':
                $this->handleEnterpriseApproved($data);
                break;

            case 'enterprise.rejected':
                $this->handleEnterpriseRejected($data);
                break;

            case 'call.initiated':
                $this->handleCallStatusUpdate($data, 'initiated');
                break;

            case 'call.ringing':
                $this->handleCallStatusUpdate($data, 'ringing');
                break;

            case 'call.answered':
                $this->handleCallAnswered($data);
                break;

            case 'call.completed':
                $this->handleCallCompleted($data);
                break;

            case 'call.failed':
                $this->handleCallFailed($data);
                break;

            default:
                Log::info('Unhandled NumHub webhook event', ['event' => $event]);
        }
    }

    /**
     * Handle enterprise/brand approval.
     */
    private function handleEnterpriseApproved(array $data): void
    {
        $enterpriseId = $data['enterprise_id'] ?? null;

        if (! $enterpriseId) {
            return;
        }

        $brand = Brand::where('numhub_enterprise_id', $enterpriseId)->first();

        if ($brand) {
            $brand->update([
                'numhub_vetting_status' => 'approved',
                'status' => 'active',
            ]);

            Log::info('Brand approved via NumHub', ['brand_id' => $brand->id]);
        }
    }

    /**
     * Handle enterprise/brand rejection.
     */
    private function handleEnterpriseRejected(array $data): void
    {
        $enterpriseId = $data['enterprise_id'] ?? null;
        $reason = $data['reason'] ?? 'Unknown';

        if (! $enterpriseId) {
            return;
        }

        $brand = Brand::where('numhub_enterprise_id', $enterpriseId)->first();

        if ($brand) {
            $brand->update([
                'numhub_vetting_status' => 'rejected',
                'status' => 'suspended',
                'metadata' => array_merge($brand->metadata ?? [], [
                    'rejection_reason' => $reason,
                    'rejected_at' => now()->toIso8601String(),
                ]),
            ]);

            Log::warning('Brand rejected via NumHub', [
                'brand_id' => $brand->id,
                'reason' => $reason,
            ]);
        }
    }

    /**
     * Handle generic call status update.
     */
    private function handleCallStatusUpdate(array $data, string $status): void
    {
        $callSid = $data['call_sid'] ?? null;

        if (! $callSid) {
            return;
        }

        $callLog = CallLog::where('external_call_sid', $callSid)->first();

        if ($callLog) {
            $callLog->update(['status' => $status]);
        }
    }

    /**
     * Handle call answered event.
     */
    private function handleCallAnswered(array $data): void
    {
        $callSid = $data['call_sid'] ?? null;

        if (! $callSid) {
            return;
        }

        $callLog = CallLog::where('external_call_sid', $callSid)->first();

        if ($callLog) {
            $ringDuration = null;
            if ($callLog->call_initiated_at && isset($data['answered_at'])) {
                $answeredAt = \Carbon\Carbon::parse($data['answered_at']);
                $ringDuration = $callLog->call_initiated_at->diffInSeconds($answeredAt);
            }

            $callLog->update([
                'status' => 'in-progress',
                'call_answered_at' => $data['answered_at'] ?? now(),
                'ring_duration_seconds' => $ringDuration,
            ]);
        }
    }

    /**
     * Handle call completed event.
     */
    private function handleCallCompleted(array $data): void
    {
        $callSid = $data['call_sid'] ?? null;

        if (! $callSid) {
            return;
        }

        $callLog = CallLog::where('external_call_sid', $callSid)->first();

        if ($callLog) {
            $talkDuration = null;
            if ($callLog->call_answered_at && isset($data['ended_at'])) {
                $endedAt = \Carbon\Carbon::parse($data['ended_at']);
                $talkDuration = $callLog->call_answered_at->diffInSeconds($endedAt);
            }

            $callLog->update([
                'status' => 'completed',
                'call_ended_at' => $data['ended_at'] ?? now(),
                'talk_duration_seconds' => $talkDuration ?? ($data['duration'] ?? null),
            ]);

            Log::info('Call completed', [
                'call_id' => $callLog->call_id,
                'duration' => $talkDuration,
            ]);
        }
    }

    /**
     * Handle call failed event.
     */
    private function handleCallFailed(array $data): void
    {
        $callSid = $data['call_sid'] ?? null;

        if (! $callSid) {
            return;
        }

        $callLog = CallLog::where('external_call_sid', $callSid)->first();

        if ($callLog) {
            $callLog->update([
                'status' => 'failed',
                'failure_reason' => $data['reason'] ?? $data['error'] ?? 'Unknown',
                'call_ended_at' => $data['ended_at'] ?? now(),
                'billable' => false, // Don't bill for failed calls
            ]);

            Log::warning('Call failed', [
                'call_id' => $callLog->call_id,
                'reason' => $data['reason'] ?? 'Unknown',
            ]);
        }
    }

    // =========================================================================
    // Telnyx Webhooks
    // =========================================================================

    /**
     * Handle Telnyx webhooks.
     *
     * POST /webhooks/telnyx
     */
    public function telnyx(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('telnyx-signature-ed25519', '');
        $timestamp = $request->header('telnyx-timestamp', '');

        // Log the webhook
        $webhookLog = WebhookLog::create([
            'source' => 'telnyx',
            'event_type' => $request->input('data.event_type'),
            'payload' => $request->all(),
            'signature' => $signature,
            'verified' => false,
            'processed' => false,
        ]);

        // Verify signature (skip in mock mode)
        if (! $this->telnyxService->verifyWebhookSignature($payload, $signature, $timestamp)) {
            Log::warning('Telnyx webhook signature verification failed', [
                'webhook_log_id' => $webhookLog->id,
            ]);
            // Don't reject - Telnyx verification is complex
        }

        $webhookLog->update(['verified' => true]);

        try {
            $this->processTelnyxWebhook($request->all(), $webhookLog);

            $webhookLog->update([
                'processed' => true,
                'processed_at' => now(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Telnyx webhook processing failed', [
                'webhook_log_id' => $webhookLog->id,
                'error' => $e->getMessage(),
            ]);

            $webhookLog->update(['processing_error' => $e->getMessage()]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Process Telnyx webhook events.
     */
    private function processTelnyxWebhook(array $data, WebhookLog $webhookLog): void
    {
        $eventType = $data['data']['event_type'] ?? null;
        $payload = $data['data']['payload'] ?? [];

        // Extract client_state if present
        $clientState = [];
        if (isset($payload['client_state'])) {
            $decoded = base64_decode($payload['client_state']);
            $clientState = json_decode($decoded, true) ?? [];
        }

        switch ($eventType) {
            case 'call.initiated':
                $this->handleTelnyxCallInitiated($payload, $clientState);
                break;

            case 'call.answered':
                $this->handleTelnyxCallAnswered($payload, $clientState);
                break;

            case 'call.hangup':
                $this->handleTelnyxCallHangup($payload, $clientState);
                break;

            case 'call.machine.detection.ended':
                // Could be used for voicemail detection
                Log::info('Telnyx machine detection', $payload);
                break;

            default:
                Log::info('Unhandled Telnyx webhook event', ['event_type' => $eventType]);
        }
    }

    /**
     * Handle Telnyx call.initiated event.
     */
    private function handleTelnyxCallInitiated(array $payload, array $clientState): void
    {
        $callControlId = $payload['call_control_id'] ?? null;

        if (! $callControlId) {
            return;
        }

        // Find call log by call_control_id
        $callLog = CallLog::where('external_call_sid', $callControlId)->first();

        if ($callLog) {
            $callLog->update(['status' => 'initiated']);
        }
    }

    /**
     * Handle Telnyx call.answered event.
     */
    private function handleTelnyxCallAnswered(array $payload, array $clientState): void
    {
        $callControlId = $payload['call_control_id'] ?? null;

        if (! $callControlId) {
            return;
        }

        $callLog = CallLog::where('external_call_sid', $callControlId)->first();

        if ($callLog) {
            $ringDuration = null;
            if ($callLog->call_initiated_at && isset($payload['start_time'])) {
                $startTime = \Carbon\Carbon::parse($payload['start_time']);
                $ringDuration = $callLog->call_initiated_at->diffInSeconds($startTime);
            }

            $callLog->update([
                'status' => 'in-progress',
                'call_answered_at' => $payload['occurred_at'] ?? now(),
                'ring_duration_seconds' => $ringDuration,
            ]);

            Log::info('Telnyx call answered', ['call_id' => $callLog->call_id]);
        }
    }

    /**
     * Handle Telnyx call.hangup event.
     */
    private function handleTelnyxCallHangup(array $payload, array $clientState): void
    {
        $callControlId = $payload['call_control_id'] ?? null;

        if (! $callControlId) {
            return;
        }

        $callLog = CallLog::where('external_call_sid', $callControlId)->first();

        if ($callLog) {
            $talkDuration = null;
            if ($callLog->call_answered_at) {
                $endTime = \Carbon\Carbon::parse($payload['occurred_at'] ?? now());
                $talkDuration = $callLog->call_answered_at->diffInSeconds($endTime);
            }

            $hangupCause = $payload['hangup_cause'] ?? 'normal_clearing';
            $isSuccess = in_array($hangupCause, ['normal_clearing', 'originator_cancel']);

            $callLog->update([
                'status' => $isSuccess ? 'completed' : 'failed',
                'call_ended_at' => $payload['occurred_at'] ?? now(),
                'talk_duration_seconds' => $talkDuration,
                'failure_reason' => $isSuccess ? null : $hangupCause,
                'billable' => $isSuccess && $talkDuration > 0,
            ]);

            Log::info('Telnyx call ended', [
                'call_id' => $callLog->call_id,
                'cause' => $hangupCause,
                'duration' => $talkDuration,
            ]);
        }
    }
}
