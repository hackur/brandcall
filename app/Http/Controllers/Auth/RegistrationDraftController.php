<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RegistrationDraft;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Registration Draft Controller - Handles auto-save during registration wizard.
 *
 * Provides endpoints for:
 * - Saving draft data on field blur/change
 * - Loading existing draft when returning to registration
 * - Clearing draft after successful registration
 */
class RegistrationDraftController extends Controller
{
    /**
     * Save or update registration draft.
     *
     * Called on field blur, step change, or explicit save.
     * Returns the draft state with last saved timestamp.
     */
    public function save(Request $request): JsonResponse
    {
        $draft = RegistrationDraft::findOrCreateForRequest($request);
        $draft->updateFromRequest($request);

        return response()->json([
            'success' => true,
            'message' => 'Draft saved',
            'draft' => [
                'id' => $draft->id,
                'current_step' => $draft->current_step,
                'last_saved_at' => $draft->last_saved_at?->toISOString(),
                'completion' => $draft->getCompletionPercentage(),
            ],
        ]);
    }

    /**
     * Load existing draft for the current session.
     *
     * Called when registration page loads to restore previous progress.
     */
    public function load(Request $request): JsonResponse
    {
        $sessionId = $request->session()->getId();
        $email = $request->query('email');

        $draft = null;

        // Try email first
        if ($email) {
            $draft = RegistrationDraft::where('email', strtolower($email))->first();
        }

        // Fall back to session
        if (! $draft) {
            $draft = RegistrationDraft::where('session_id', $sessionId)->first();
        }

        if (! $draft) {
            return response()->json([
                'success' => true,
                'draft' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'draft' => [
                'current_step' => $draft->current_step,
                'name' => $draft->name,
                'email' => $draft->email,
                'company_name' => $draft->company_name,
                'company_website' => $draft->company_website,
                'company_phone' => $draft->company_phone,
                'company_address' => $draft->company_address,
                'company_city' => $draft->company_city,
                'company_state' => $draft->company_state,
                'company_zip' => $draft->company_zip,
                'company_country' => $draft->company_country,
                'industry' => $draft->industry,
                'monthly_call_volume' => $draft->monthly_call_volume,
                'use_case' => $draft->use_case,
                'current_provider' => $draft->current_provider,
                'has_stir_shaken' => $draft->uses_stir_shaken,
                'primary_phone' => $draft->primary_phone,
                'phone_ownership' => $draft->phone_ownership,
                'last_saved_at' => $draft->last_saved_at?->toISOString(),
                'completion' => $draft->getCompletionPercentage(),
            ],
        ]);
    }

    /**
     * Clear draft (called after successful registration).
     */
    public function clear(Request $request): JsonResponse
    {
        $sessionId = $request->session()->getId();
        $email = $request->input('email');

        RegistrationDraft::where('session_id', $sessionId)
            ->orWhere('email', $email)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Draft cleared',
        ]);
    }
}
