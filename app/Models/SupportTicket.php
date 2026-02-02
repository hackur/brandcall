<?php

/**
 * BrandCall - Branded Caller ID SaaS Platform
 *
 * @package    BrandCall
 * @subpackage Models
 * @author     BrandCall Development Team
 * @copyright  2024-2026 BrandCall
 * @license    Proprietary
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Support Ticket Model - Customer support request tracking.
 *
 * Manages customer support tickets throughout their lifecycle.
 * Tickets can be created by users during onboarding or after
 * approval, and are managed by admin staff through the Filament panel.
 *
 * Ticket Lifecycle:
 * 1. User creates ticket (status: open)
 * 2. Admin assigns/responds (status: in_progress)
 * 3. Admin awaits user response (status: waiting)
 * 4. Issue resolved (status: resolved)
 * 5. Ticket closed (status: closed)
 *
 * Priority Levels:
 * - Low: General inquiries, can wait 48-72 hours
 * - Medium: Standard issues, respond within 24 hours
 * - High: Urgent issues affecting service, respond within 4 hours
 * - Urgent: Critical issues, immediate attention required
 *
 * Categories:
 * - General: General inquiries and questions
 * - Billing: Payment, subscription, invoice issues
 * - Technical: API, integration, platform issues
 * - KYC: Verification and compliance questions
 * - Feature: Feature requests and suggestions
 *
 * @property int $id Primary key
 * @property int $user_id Ticket creator
 * @property int|null $tenant_id Associated tenant
 * @property string $ticket_number Unique ticket reference (TKT-XXXXX)
 * @property string $subject Brief description of issue
 * @property string $description Detailed issue description
 * @property string $category Issue category
 * @property string $priority Urgency level
 * @property string $status Current ticket status
 * @property int|null $assigned_to Assigned admin user
 * @property \Carbon\Carbon|null $resolved_at When issue was resolved
 * @property \Carbon\Carbon|null $closed_at When ticket was closed
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read User $user Ticket creator
 * @property-read Tenant|null $tenant Associated tenant
 * @property-read User|null $assignee Assigned admin
 * @property-read \Illuminate\Database\Eloquent\Collection|TicketReply[] $replies
 */
class SupportTicket extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Priority Constants
    |--------------------------------------------------------------------------
    */

    /** @var string Low priority - general inquiries */
    public const PRIORITY_LOW = 'low';

    /** @var string Medium priority - standard issues (default) */
    public const PRIORITY_MEDIUM = 'medium';

    /** @var string High priority - urgent issues */
    public const PRIORITY_HIGH = 'high';

    /** @var string Urgent priority - critical issues */
    public const PRIORITY_URGENT = 'urgent';

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */

    /** @var string Newly created, awaiting response */
    public const STATUS_OPEN = 'open';

    /** @var string Being actively worked on */
    public const STATUS_IN_PROGRESS = 'in_progress';

    /** @var string Awaiting customer response */
    public const STATUS_WAITING = 'waiting';

    /** @var string Issue resolved, pending closure */
    public const STATUS_RESOLVED = 'resolved';

    /** @var string Ticket closed (terminal state) */
    public const STATUS_CLOSED = 'closed';

    /*
    |--------------------------------------------------------------------------
    | Category Constants
    |--------------------------------------------------------------------------
    */

    /** @var string General inquiries */
    public const CATEGORY_GENERAL = 'general';

    /** @var string Billing and payment issues */
    public const CATEGORY_BILLING = 'billing';

    /** @var string Technical and integration issues */
    public const CATEGORY_TECHNICAL = 'technical';

    /** @var string KYC and verification questions */
    public const CATEGORY_KYC = 'kyc';

    /** @var string Feature requests */
    public const CATEGORY_FEATURE = 'feature';

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
        'user_id',
        'tenant_id',
        'ticket_number',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'assigned_to',
        'resolved_at',
        'closed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */

    /**
     * Bootstrap the model and its traits.
     *
     * Automatically generates a unique ticket number on creation
     * using the format TKT-XXXXXXXXX.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (SupportTicket $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-' . strtoupper(uniqid());
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user who created this ticket.
     *
     * @return BelongsTo<User, SupportTicket>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tenant this ticket belongs to.
     *
     * @return BelongsTo<Tenant, SupportTicket>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the admin user assigned to this ticket.
     *
     * @return BelongsTo<User, SupportTicket>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all replies on this ticket.
     *
     * Replies are ordered chronologically and include both
     * user and admin responses.
     *
     * @return HasMany<TicketReply>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Status Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the ticket is still open and active.
     *
     * Open tickets require attention and haven't been resolved
     * or closed yet.
     *
     * @return bool True if ticket is open, in_progress, or waiting
     */
    public function isOpen(): bool
    {
        return in_array($this->status, [
            self::STATUS_OPEN,
            self::STATUS_IN_PROGRESS,
            self::STATUS_WAITING,
        ]);
    }

    /**
     * Check if the ticket has been closed.
     *
     * Closed tickets are either resolved or manually closed
     * and don't require further action.
     *
     * @return bool True if ticket is resolved or closed
     */
    public function isClosed(): bool
    {
        return in_array($this->status, [
            self::STATUS_RESOLVED,
            self::STATUS_CLOSED,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get human-readable label for a priority level.
     *
     * @param string $priority Priority constant value
     * @return string Human-readable label
     */
    public static function getPriorityLabel(string $priority): string
    {
        return match ($priority) {
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
            default => $priority,
        };
    }

    /**
     * Get human-readable label for a category.
     *
     * @param string $category Category constant value
     * @return string Human-readable label
     */
    public static function getCategoryLabel(string $category): string
    {
        return match ($category) {
            self::CATEGORY_GENERAL => 'General Inquiry',
            self::CATEGORY_BILLING => 'Billing & Payments',
            self::CATEGORY_TECHNICAL => 'Technical Support',
            self::CATEGORY_KYC => 'KYC / Verification',
            self::CATEGORY_FEATURE => 'Feature Request',
            default => $category,
        };
    }

    /**
     * Get human-readable label for a status.
     *
     * @param string $status Status constant value
     * @return string Human-readable label
     */
    public static function getStatusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_OPEN => 'Open',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_WAITING => 'Waiting on Customer',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED => 'Closed',
            default => $status,
        };
    }

    /**
     * Get all priority options for dropdowns.
     *
     * @return array<string, string> Priority value => label
     */
    public static function getPriorityOptions(): array
    {
        return [
            self::PRIORITY_LOW => self::getPriorityLabel(self::PRIORITY_LOW),
            self::PRIORITY_MEDIUM => self::getPriorityLabel(self::PRIORITY_MEDIUM),
            self::PRIORITY_HIGH => self::getPriorityLabel(self::PRIORITY_HIGH),
            self::PRIORITY_URGENT => self::getPriorityLabel(self::PRIORITY_URGENT),
        ];
    }

    /**
     * Get all category options for dropdowns.
     *
     * @return array<string, string> Category value => label
     */
    public static function getCategoryOptions(): array
    {
        return [
            self::CATEGORY_GENERAL => self::getCategoryLabel(self::CATEGORY_GENERAL),
            self::CATEGORY_BILLING => self::getCategoryLabel(self::CATEGORY_BILLING),
            self::CATEGORY_TECHNICAL => self::getCategoryLabel(self::CATEGORY_TECHNICAL),
            self::CATEGORY_KYC => self::getCategoryLabel(self::CATEGORY_KYC),
            self::CATEGORY_FEATURE => self::getCategoryLabel(self::CATEGORY_FEATURE),
        ];
    }

    /**
     * Get all status options for dropdowns.
     *
     * @return array<string, string> Status value => label
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_OPEN => self::getStatusLabel(self::STATUS_OPEN),
            self::STATUS_IN_PROGRESS => self::getStatusLabel(self::STATUS_IN_PROGRESS),
            self::STATUS_WAITING => self::getStatusLabel(self::STATUS_WAITING),
            self::STATUS_RESOLVED => self::getStatusLabel(self::STATUS_RESOLVED),
            self::STATUS_CLOSED => self::getStatusLabel(self::STATUS_CLOSED),
        ];
    }
}
