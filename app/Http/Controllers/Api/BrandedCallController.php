<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\VoiceProvider;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CallLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * API Controller for Branded Calls.
 *
 * Handles call initiation and status retrieval via the public API.
 * Authentication is via brand-specific API keys (Bearer token).
 */
class BrandedCallController extends Controller
{
    public function __construct(
        private VoiceProvider $voiceProvider
    ) {}

    /**
     * Initiate a branded call.
     *
     * POST /api/v1/brands/{slug}/calls
     *
     * @param string $slug Brand slug
     */
    public function initiate(Request $request, string $slug): JsonResponse
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'from' => ['required', 'string', 'regex:/^\+[1-9]\d{1,14}$/'], // E.164
            'to' => ['required', 'string', 'regex:/^\+[1-9]\d{1,14}$/'],   // E.164
            'call_reason' => ['nullable', 'string', 'max:64'],
            'attestation_level' => ['nullable', 'string', 'in:A,B,C'],
            'metadata' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'details' => $validator->errors(),
            ], 422);
        }

        // Find brand by slug and verify API key
        $brand = Brand::where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (! $brand) {
            return response()->json([
                'success' => false,
                'error' => 'Brand not found or inactive',
            ], 404);
        }

        // Verify API key from Authorization header
        $apiKey = $request->bearerToken();
        if (! $apiKey || ! hash_equals($brand->api_key, $apiKey)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid API key',
            ], 401);
        }

        // Verify the 'from' number belongs to this brand
        $fromNumber = $brand->phoneNumbers()
            ->where('phone_number', $request->input('from'))
            ->where('status', 'active')
            ->first();

        if (! $fromNumber) {
            return response()->json([
                'success' => false,
                'error' => 'The "from" number is not registered to this brand',
            ], 400);
        }

        // Calculate tier price
        $tierPrice = $this->calculateTierPrice($brand->tenant_id);

        // Create call log entry
        $callLog = CallLog::create([
            'tenant_id' => $brand->tenant_id,
            'brand_id' => $brand->id,
            'brand_phone_number_id' => $fromNumber->id,
            'call_id' => 'call_' . Str::lower(Str::random(20)),
            'from_number' => $request->input('from'),
            'to_number' => $request->input('to'),
            'attestation_level' => $request->input('attestation_level', $brand->default_attestation_level ?? 'B'),
            'status' => 'initiated',
            'branded_call' => true,
            'call_initiated_at' => now(),
            'tier_price' => $tierPrice,
            'cost' => $tierPrice,
            'billable' => true,
            'rcd_payload' => [
                'brand_name' => $brand->display_name,
                'logo_url' => $brand->logo_url,
                'call_reason' => $request->input('call_reason', $brand->call_reason),
            ],
        ]);

        // Initiate call via voice provider (Telnyx, NumHub, etc.)
        $result = $this->voiceProvider->call(
            $brand,
            $request->input('from'),
            $request->input('to'),
            $request->input('call_reason'),
            ['metadata' => $request->input('metadata', [])]
        );

        if (! $result['success']) {
            // Update call log as failed
            $callLog->update([
                'status' => 'failed',
                'failure_reason' => $result['error'],
                'billable' => false,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to initiate call',
                'details' => $result['error'],
            ], 500);
        }

        // Update call log with NumHub SID
        $callLog->update([
            'external_call_sid' => $result['call_sid'],
            'numhub_response' => $result,
        ]);

        return response()->json([
            'success' => true,
            'call_id' => $callLog->call_id,
            'external_call_sid' => $result['call_sid'],
            'status' => 'initiated',
            'cost' => $tierPrice,
            'attestation_level' => $result['attestation_level'],
            'brand' => [
                'name' => $brand->name,
                'display_name' => $brand->display_name,
                'logo_url' => $brand->logo_url,
                'call_reason' => $request->input('call_reason', $brand->call_reason),
            ],
            'stir_shaken' => [
                'enabled' => true,
                'verified' => true,
            ],
        ], 201);
    }

    /**
     * Get call status.
     *
     * GET /api/v1/brands/{slug}/calls/{callId}
     *
     * @param string $slug   Brand slug
     * @param string $callId Call ID
     */
    public function show(Request $request, string $slug, string $callId): JsonResponse
    {
        // Find brand and verify API key
        $brand = Brand::where('slug', $slug)->first();

        if (! $brand) {
            return response()->json([
                'success' => false,
                'error' => 'Brand not found',
            ], 404);
        }

        $apiKey = $request->bearerToken();
        if (! $apiKey || ! hash_equals($brand->api_key, $apiKey)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid API key',
            ], 401);
        }

        // Find call log
        $callLog = CallLog::where('brand_id', $brand->id)
            ->where('call_id', $callId)
            ->first();

        if (! $callLog) {
            return response()->json([
                'success' => false,
                'error' => 'Call not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'call_id' => $callLog->call_id,
            'external_call_sid' => $callLog->external_call_sid,
            'status' => $callLog->status,
            'from' => $callLog->from_number,
            'to' => $callLog->to_number,
            'attestation_level' => $callLog->attestation_level,
            'duration' => [
                'ring_seconds' => $callLog->ring_duration_seconds,
                'talk_seconds' => $callLog->talk_duration_seconds,
                'total_seconds' => ($callLog->ring_duration_seconds ?? 0) + ($callLog->talk_duration_seconds ?? 0),
            ],
            'cost' => $callLog->cost,
            'timestamps' => [
                'initiated_at' => $callLog->call_initiated_at?->toIso8601String(),
                'answered_at' => $callLog->call_answered_at?->toIso8601String(),
                'ended_at' => $callLog->call_ended_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * List calls for a brand.
     *
     * GET /api/v1/brands/{slug}/calls
     *
     * @param string $slug Brand slug
     */
    public function index(Request $request, string $slug): JsonResponse
    {
        // Find brand and verify API key
        $brand = Brand::where('slug', $slug)->first();

        if (! $brand) {
            return response()->json([
                'success' => false,
                'error' => 'Brand not found',
            ], 404);
        }

        $apiKey = $request->bearerToken();
        if (! $apiKey || ! hash_equals($brand->api_key, $apiKey)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid API key',
            ], 401);
        }

        $calls = CallLog::where('brand_id', $brand->id)
            ->orderBy('created_at', 'desc')
            ->limit($request->input('limit', 50))
            ->get()
            ->map(fn ($call) => [
                'call_id' => $call->call_id,
                'status' => $call->status,
                'from' => $call->from_number,
                'to' => $call->to_number,
                'cost' => $call->cost,
                'initiated_at' => $call->call_initiated_at?->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'calls' => $calls,
            'total' => CallLog::where('brand_id', $brand->id)->count(),
        ]);
    }

    /**
     * Calculate the tier price based on tenant's monthly usage.
     */
    private function calculateTierPrice(int $tenantId): float
    {
        $currentMonth = now()->format('Y-m');
        $monthlyCallCount = CallLog::where('tenant_id', $tenantId)
            ->where('billable', true)
            ->whereRaw("strftime('%Y-%m', call_initiated_at) = ?", [$currentMonth])
            ->count();

        // Tier pricing
        return match (true) {
            $monthlyCallCount >= 10_000_000 => 0.025,
            $monthlyCallCount >= 1_000_000 => 0.035,
            $monthlyCallCount >= 100_000 => 0.050,
            $monthlyCallCount >= 10_000 => 0.065,
            default => 0.075,
        };
    }
}
