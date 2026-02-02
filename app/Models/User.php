<?php

/**
 * BrandCall - Branded Caller ID SaaS Platform.
 *
 * @author     BrandCall Development Team
 * @copyright  2024-2026 BrandCall
 * @license    Proprietary
 */

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model - Core authentication and profile entity.
 *
 * Represents a user account in the BrandCall platform. Users go through
 * a multi-step onboarding process:
 *
 * 1. Registration (status: pending)
 * 2. Email verification
 * 3. Company profile completion
 * 4. KYC document upload
 * 5. Admin approval (status: approved)
 *
 * Users implement FilamentUser for admin panel access control and
 * MustVerifyEmail for email verification workflow.
 *
 * @property int                 $id                      Primary key
 * @property string              $name                    User's full name
 * @property string              $email                   User's email address (unique)
 * @property string              $password                Hashed password
 * @property int|null            $tenant_id               Associated tenant for multi-tenancy
 * @property string              $status                  Account status (pending|verified|approved|suspended)
 * @property string|null         $phone                   Personal phone number
 * @property string|null         $company_name            Business name
 * @property string|null         $company_website         Business website URL
 * @property string|null         $company_phone           Business phone number
 * @property string|null         $company_address         Street address
 * @property string|null         $company_city            City
 * @property string|null         $company_state           State/Province
 * @property string|null         $company_zip             Postal/ZIP code
 * @property string|null         $industry                Business industry vertical
 * @property string|null         $monthly_call_volume     Expected monthly call volume tier
 * @property string|null         $use_case                Primary use case for branded caller ID
 * @property string|null         $current_provider        Current telephony provider
 * @property string|null         $uses_stir_shaken        Whether business uses STIR/SHAKEN
 * @property \Carbon\Carbon|null $email_verified_at       Email verification timestamp
 * @property \Carbon\Carbon|null $kyc_submitted_at        KYC submission timestamp
 * @property \Carbon\Carbon|null $kyc_approved_at         KYC approval timestamp
 * @property \Carbon\Carbon|null $onboarding_completed_at Onboarding completion timestamp
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property-read Tenant|null $tenant Associated tenant
 * @property-read \Illuminate\Database\Eloquent\Collection|Document[] $documents
 * @property-read \Illuminate\Database\Eloquent\Collection|SupportTicket[] $supportTickets
 *
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 */
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Account Status Constants
    |--------------------------------------------------------------------------
    |
    | These constants define the lifecycle states of a user account.
    | Users progress through: pending -> verified -> approved
    | Suspended is a terminal state requiring admin intervention.
    |
    */

    /** @var string Initial state after registration, awaiting email verification */
    public const STATUS_PENDING = 'pending';

    /** @var string Email verified, KYC submitted, awaiting admin approval */
    public const STATUS_VERIFIED = 'verified';

    /** @var string Fully approved with full platform access */
    public const STATUS_APPROVED = 'approved';

    /** @var string Account suspended by admin */
    public const STATUS_SUSPENDED = 'suspended';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment Protection
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'status',
        'phone',
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
        'kyc_submitted_at',
        'kyc_approved_at',
        'onboarding_completed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'kyc_submitted_at' => 'datetime',
            'kyc_approved_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the tenant this user belongs to.
     *
     * In multi-tenant mode, users are associated with a tenant (organization).
     * The tenant owns the subscription and billing relationship.
     *
     * @return BelongsTo<Tenant, User>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get all documents uploaded by this user.
     *
     * Documents are used for KYC verification and compliance purposes.
     * Includes business licenses, tax IDs, government IDs, etc.
     *
     * @return HasMany<Document>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get all support tickets created by this user.
     *
     * @return HasMany<SupportTicket>
     */
    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Status & Verification Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the user's email address has been verified.
     *
     * Email verification is required before users can upload documents
     * or access most platform features.
     *
     * @return bool True if email is verified
     */
    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Check if the user has submitted KYC documents for review.
     *
     * KYC (Know Your Customer) submission is required for compliance
     * with telecommunications regulations and fraud prevention.
     *
     * @return bool True if KYC has been submitted
     */
    public function hasCompletedKyc(): bool
    {
        return $this->kyc_submitted_at !== null;
    }

    /**
     * Check if the user account is fully approved.
     *
     * Approved users have completed all verification steps and
     * have been manually approved by an administrator.
     *
     * @return bool True if account is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the user can access the main application dashboard.
     *
     * Dashboard access requires both email verification and admin approval.
     * Users in onboarding see a limited dashboard until fully approved.
     *
     * @return bool True if user can access full dashboard
     */
    public function canAccessDashboard(): bool
    {
        return $this->isEmailVerified() && $this->isApproved();
    }

    /*
    |--------------------------------------------------------------------------
    | Onboarding Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate the onboarding completion percentage.
     *
     * Tracks progress through the four onboarding steps:
     * 1. Email verification
     * 2. Company profile completion
     * 3. KYC document upload
     * 4. KYC submission
     *
     * @return int Percentage complete (0-100)
     */
    public function getOnboardingProgress(): int
    {
        $steps = [
            $this->isEmailVerified(),
            $this->company_name !== null,
            $this->documents()->whereIn('type', ['business_license', 'tax_id', 'kyc', 'drivers_license', 'government_id'])->exists(),
            $this->kyc_submitted_at !== null,
        ];

        $completed = count(array_filter($steps));

        return (int) (($completed / count($steps)) * 100);
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Determine if the user can access a Filament admin panel.
     *
     * Only users with the 'super-admin' role can access the admin panel.
     * This is enforced at the panel level for security.
     *
     * @param Panel $panel The Filament panel being accessed
     *
     * @return bool True if user can access the panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('super-admin');
        }

        return true;
    }

    /**
     * Check if the user is the owner of their tenant.
     *
     * Tenant owners have full administrative control over their
     * organization, including billing and user management.
     *
     * @return bool True if user is tenant owner
     */
    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    /**
     * Check if the user has administrative privileges for their tenant.
     *
     * Tenant admins can manage users, brands, and phone numbers
     * within their organization.
     *
     * @return bool True if user is owner or admin
     */
    public function isTenantAdmin(): bool
    {
        return $this->hasAnyRole(['owner', 'admin']);
    }
}
