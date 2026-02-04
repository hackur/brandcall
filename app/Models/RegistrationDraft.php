<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Registration Draft - Stores in-progress registration wizard data.
 *
 * Allows users to save their progress and resume registration later.
 * Identified by session_id or email. Data is transferred to User model
 * upon successful registration.
 *
 * Field Naming Convention:
 * - Step 1 (Account): name, email
 * - Step 2 (Business): company_* fields
 * - Step 3 (Qualification): industry, monthly_call_volume, use_case, current_provider, uses_stir_shaken
 * - Step 4 (Phone): primary_phone, phone_ownership
 *
 * @property int                 $id
 * @property string|null         $session_id
 * @property string|null         $email
 * @property int                 $current_step
 * @property string|null         $name
 * @property string|null         $company_name
 * @property string|null         $company_website
 * @property string|null         $company_phone
 * @property string|null         $company_address
 * @property string|null         $company_city
 * @property string|null         $company_state
 * @property string|null         $company_zip
 * @property string              $company_country
 * @property string|null         $industry
 * @property string|null         $monthly_call_volume
 * @property string|null         $use_case
 * @property string|null         $current_provider
 * @property string|null         $uses_stir_shaken
 * @property string|null         $primary_phone
 * @property string|null         $phone_ownership
 * @property string|null         $ip_address
 * @property string|null         $user_agent
 * @property \Carbon\Carbon|null $last_saved_at
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 */
class RegistrationDraft extends Model
{
    /**
     * Fields that can be mass assigned.
     */
    protected $fillable = [
        'session_id',
        'email',
        'current_step',
        'name',
        'company_name',
        'company_website',
        'company_phone',
        'company_address',
        'company_city',
        'company_state',
        'company_zip',
        'company_country',
        'industry',
        'monthly_call_volume',
        'use_case',
        'current_provider',
        'uses_stir_shaken',
        'primary_phone',
        'phone_ownership',
        'ip_address',
        'user_agent',
        'last_saved_at',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'current_step' => 'integer',
            'last_saved_at' => 'datetime',
        ];
    }

    /**
     * Fields that map to the User model.
     * Used when transferring draft data to a new user.
     */
    public const USER_FIELDS = [
        'name',
        'company_name',
        'company_website',
        'company_phone',
        'company_address',
        'company_city',
        'company_state',
        'company_zip',
        'industry',
        'monthly_call_volume',
        'use_case',
        'current_provider',
        'uses_stir_shaken',
    ];

    /**
     * Find or create a draft for the current session/email.
     */
    public static function findOrCreateForRequest(Request $request): self
    {
        $sessionId = $request->session()->getId();
        $email = $request->input('email');

        // Try to find by email first (if provided)
        if ($email) {
            $draft = self::where('email', strtolower($email))->first();
            if ($draft) {
                // Update session_id to current session
                $draft->update(['session_id' => $sessionId]);

                return $draft;
            }
        }

        // Try to find by session_id
        $draft = self::where('session_id', $sessionId)->first();
        if ($draft) {
            return $draft;
        }

        // Create new draft
        return self::create([
            'session_id' => $sessionId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Update draft with request data.
     */
    public function updateFromRequest(Request $request): self
    {
        $data = $request->only([
            'current_step',
            'name',
            'email',
            'company_name',
            'company_website',
            'company_phone',
            'company_address',
            'company_city',
            'company_state',
            'company_zip',
            'company_country',
            'industry',
            'monthly_call_volume',
            'use_case',
            'current_provider',
            'uses_stir_shaken',
            'primary_phone',
            'phone_ownership',
        ]);

        // Normalize email to lowercase
        if (isset($data['email'])) {
            $data['email'] = strtolower($data['email']);
        }

        // Map has_stir_shaken to uses_stir_shaken (frontend uses different name)
        if ($request->has('has_stir_shaken')) {
            $data['uses_stir_shaken'] = $request->input('has_stir_shaken');
        }

        $data['last_saved_at'] = now();
        $data['ip_address'] = $request->ip();

        $this->update($data);

        return $this->fresh();
    }

    /**
     * Get data to transfer to a new User.
     */
    public function getUserData(): array
    {
        return collect(self::USER_FIELDS)
            ->mapWithKeys(fn ($field) => [$field => $this->$field])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->toArray();
    }

    /**
     * Delete draft after successful registration.
     */
    public function consume(): void
    {
        $this->delete();
    }

    /**
     * Get percentage completion (0-100).
     */
    public function getCompletionPercentage(): int
    {
        $fields = [
            'name', 'email', // Step 1
            'company_name', // Step 2 (required)
            'company_website', 'company_phone', 'company_address', // Step 2 (optional)
            'industry', 'monthly_call_volume', 'use_case', // Step 3
            'primary_phone', // Step 4
        ];

        $filled = collect($fields)->filter(fn ($field) => ! empty($this->$field))->count();

        return (int) round(($filled / count($fields)) * 100);
    }

    /**
     * Prune old drafts (older than 30 days).
     */
    public static function pruneOld(): int
    {
        return self::where('updated_at', '<', now()->subDays(30))->delete();
    }
}
