<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RegistrationDraft;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Registered User Controller - Handles new user registration.
 *
 * Manages the multi-step registration wizard:
 * 1. Account credentials (name, email, password)
 * 2. Business information (company details, address)
 * 3. Qualification/KYC (industry, volume, use case)
 * 4. Phone numbers to brand
 *
 * Works with RegistrationDraft to persist progress and restore on return.
 */
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * Creates the user with all wizard data including business info and KYC fields.
     * Transfers data from RegistrationDraft if available, then deletes the draft.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Normalize email to lowercase before validation
        $request->merge([
            'email' => strtolower($request->email),
        ]);

        // Validate required fields (step 1)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Validate optional fields (steps 2-4)
        $validated = $request->validate([
            // Step 2: Business Info
            'company_name' => 'nullable|string|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string|max:255',
            'company_city' => 'nullable|string|max:100',
            'company_state' => 'nullable|string|max:100',
            'company_zip' => 'nullable|string|max:20',
            // Step 3: Qualification/KYC
            'industry' => 'nullable|string|max:100',
            'monthly_call_volume' => 'nullable|string|max:50',
            'use_case' => 'nullable|string|max:100',
            'current_provider' => 'nullable|string|max:100',
            'has_stir_shaken' => 'nullable|string|in:yes,no,not_sure',
            // Step 4: Phone
            'primary_phone' => 'nullable|string|max:20',
            'phone_ownership' => 'nullable|string|in:own,provider,need',
        ]);

        // Map frontend field name to database field name
        if (isset($validated['has_stir_shaken'])) {
            $validated['uses_stir_shaken'] = $validated['has_stir_shaken'];
            unset($validated['has_stir_shaken']);
        }

        // Check for existing draft to merge any data not in request
        $draft = RegistrationDraft::where('email', $request->email)
            ->orWhere('session_id', $request->session()->getId())
            ->first();

        // Merge draft data (request data takes precedence)
        if ($draft) {
            $draftData = $draft->getUserData();
            $validated = array_merge($draftData, array_filter($validated, fn ($v) => $v !== null && $v !== ''));
        }

        // Create the user with all collected data
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Business info
            'company_name' => $validated['company_name'] ?? null,
            'company_website' => $validated['company_website'] ?? null,
            'company_phone' => $validated['company_phone'] ?? null,
            'company_address' => $validated['company_address'] ?? null,
            'company_city' => $validated['company_city'] ?? null,
            'company_state' => $validated['company_state'] ?? null,
            'company_zip' => $validated['company_zip'] ?? null,
            // KYC/Qualification
            'industry' => $validated['industry'] ?? null,
            'monthly_call_volume' => $validated['monthly_call_volume'] ?? null,
            'use_case' => $validated['use_case'] ?? null,
            'current_provider' => $validated['current_provider'] ?? null,
            'uses_stir_shaken' => $validated['uses_stir_shaken'] ?? null,
            // Status
            'status' => 'pending',
        ]);

        // Clean up draft after successful registration
        if ($draft) {
            $draft->consume();
        }

        event(new Registered($user));

        Auth::login($user);

        // Go directly to onboarding (doesn't require verified email)
        return redirect(route('onboarding.index', absolute: false));
    }
}
