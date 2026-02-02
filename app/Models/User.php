<?php

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

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, HasRoles, Notifiable;

    /**
     * Account status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_SUSPENDED = 'suspended';

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

    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Check if user's email is verified.
     */
    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Check if user has completed KYC.
     */
    public function hasCompletedKyc(): bool
    {
        return $this->kyc_submitted_at !== null;
    }

    /**
     * Check if user is approved for full access.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if user can access the main dashboard.
     */
    public function canAccessDashboard(): bool
    {
        return $this->isEmailVerified() && $this->isApproved();
    }

    /**
     * Get the onboarding completion percentage.
     */
    public function getOnboardingProgress(): int
    {
        $steps = [
            $this->isEmailVerified(),
            $this->company_name !== null,
            $this->documents()->where('type', 'kyc')->exists(),
            $this->kyc_submitted_at !== null,
        ];

        $completed = count(array_filter($steps));
        return (int) (($completed / count($steps)) * 100);
    }

    /**
     * Determine if the user can access the Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('super-admin');
        }

        return true;
    }

    /**
     * Check if user is tenant owner.
     */
    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    /**
     * Check if user has admin capabilities for their tenant.
     */
    public function isTenantAdmin(): bool
    {
        return $this->hasAnyRole(['owner', 'admin']);
    }
}
