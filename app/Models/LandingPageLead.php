<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Lead submission from a landing page.
 *
 * @property int $id
 * @property int $landing_page_id
 * @property string|null $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $company
 * @property string|null $message
 * @property array|null $custom_fields
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $referrer
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string $status
 * @property string|null $notes
 */
class LandingPageLead extends Model
{
    protected $fillable = [
        'landing_page_id',
        'name',
        'email',
        'phone',
        'company',
        'message',
        'custom_fields',
        'ip_address',
        'user_agent',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'status',
        'notes',
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    public const STATUS_NEW = 'new';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_QUALIFIED = 'qualified';
    public const STATUS_CONVERTED = 'converted';
    public const STATUS_CLOSED = 'closed';

    public const STATUSES = [
        self::STATUS_NEW => 'New',
        self::STATUS_CONTACTED => 'Contacted',
        self::STATUS_QUALIFIED => 'Qualified',
        self::STATUS_CONVERTED => 'Converted',
        self::STATUS_CLOSED => 'Closed',
    ];

    /**
     * Get the landing page this lead came from.
     */
    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class);
    }

    /**
     * Check if this is a new lead.
     */
    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }
}
