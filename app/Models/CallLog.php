<?php

/**
 * BrandCall - Branded Caller ID SaaS Platform.
 *
 * @author     BrandCall Development Team
 * @copyright  2024-2026 BrandCall
 * @license    Proprietary
 */

declare(strict_types=1);

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CallLog Model - Records every call made through the platform.
 *
 * Tracks all call activity including branded calls, STIR/SHAKEN attestation,
 * Rich Call Data (RCD), duration, cost, and spam scoring. Used for:
 * - Usage tracking and billing
 * - Analytics and reporting
 * - Compliance auditing
 * - Spam detection monitoring
 *
 * Call Lifecycle:
 * 1. initiated: Call request received
 * 2. ringing: Destination phone is ringing
 * 3. in-progress: Call connected
 * 4. completed: Call ended normally
 * 5. failed/busy/no-answer: Call did not connect
 *
 * STIR/SHAKEN Attestation Levels:
 * - A: Full attestation (carrier verified caller owns number)
 * - B: Partial attestation (carrier verified caller relationship)
 * - C: Gateway attestation (no verification)
 *
 * @property int                 $id                     Primary key
 * @property int                 $tenant_id              Owning tenant
 * @property int                 $brand_id               Associated brand
 * @property int|null            $brand_phone_number_id  Source phone number
 * @property string              $call_id                Internal call identifier (call_xxxxx)
 * @property string|null         $external_call_sid      Provider's call identifier
 * @property string              $from_number            Originating phone (E.164)
 * @property string              $to_number              Destination phone (E.164)
 * @property string              $direction              inbound|outbound
 * @property string              $attestation_level      STIR/SHAKEN level (A|B|C)
 * @property bool                $stir_shaken_verified   Whether SHAKEN was verified
 * @property string|null         $identity_header        SIP Identity header
 * @property string              $status                 Call status
 * @property string|null         $failure_reason         Reason for failed calls
 * @property bool                $branded_call           Whether RCD was applied
 * @property array|null          $rcd_payload            Rich Call Data sent
 * @property \Carbon\Carbon|null $call_initiated_at      When call was initiated
 * @property \Carbon\Carbon|null $call_answered_at       When call was answered
 * @property \Carbon\Carbon|null $call_ended_at          When call ended
 * @property int|null            $ring_duration_seconds  Seconds until answered
 * @property int|null            $talk_duration_seconds  Conversation duration
 * @property int|null            $total_duration_seconds Total call duration
 * @property array|null          $spam_scores            Carrier spam scoring data
 * @property bool                $flagged_as_spam        Whether flagged as spam
 * @property string|null         $spam_label             Spam label (if any)
 * @property float               $cost                   Calculated call cost
 * @property float               $tier_price             Price per call at time of call
 * @property bool                $billable               Whether to bill for this call
 * @property array|null          $numhub_response        Raw provider response
 * @property array|null          $carrier_metadata       Carrier-specific metadata
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property-read Tenant           $tenant      Owning tenant
 * @property-read Brand            $brand       Associated brand
 * @property-read BrandPhoneNumber $phoneNumber Source phone number
 */
class CallLog extends Model
{
    use HasFactory;

    /**
     * Call status constants.
     */
    public const STATUS_INITIATED = 'initiated';

    public const STATUS_RINGING = 'ringing';

    public const STATUS_IN_PROGRESS = 'in-progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_BUSY = 'busy';

    public const STATUS_NO_ANSWER = 'no-answer';

    public const STATUS_CANCELED = 'canceled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'brand_id',
        'brand_phone_number_id',
        'call_id',
        'external_call_sid',
        'from_number',
        'to_number',
        'direction',
        'attestation_level',
        'stir_shaken_verified',
        'identity_header',
        'status',
        'failure_reason',
        'branded_call',
        'rcd_payload',
        'call_initiated_at',
        'call_answered_at',
        'call_ended_at',
        'ring_duration_seconds',
        'talk_duration_seconds',
        'total_duration_seconds',
        'spam_scores',
        'flagged_as_spam',
        'spam_label',
        'cost',
        'tier_price',
        'billable',
        'numhub_response',
        'carrier_metadata',
    ];

    protected $casts = [
        'stir_shaken_verified' => 'boolean',
        'branded_call' => 'boolean',
        'rcd_payload' => 'array',
        'call_initiated_at' => 'datetime',
        'call_answered_at' => 'datetime',
        'call_ended_at' => 'datetime',
        'spam_scores' => 'array',
        'flagged_as_spam' => 'boolean',
        'cost' => 'decimal:4',
        'tier_price' => 'decimal:4',
        'billable' => 'boolean',
        'numhub_response' => 'array',
        'carrier_metadata' => 'array',
    ];

    /**
     * Bootstrap the model and apply global scopes.
     *
     * Automatically applies TenantScope for multi-tenant isolation.
     * Sets tenant_id from container binding if not provided.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (CallLog $log) {
            if (! $log->tenant_id && app()->bound('current_tenant')) {
                $log->tenant_id = app('current_tenant')->id;
            }
        });
    }

    /**
     * Get the tenant that owns this call log.
     *
     * @return BelongsTo<Tenant, CallLog>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the brand associated with this call.
     *
     * @return BelongsTo<Brand, CallLog>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the phone number used for this call.
     *
     * @return BelongsTo<BrandPhoneNumber, CallLog>
     */
    public function phoneNumber(): BelongsTo
    {
        return $this->belongsTo(BrandPhoneNumber::class, 'brand_phone_number_id');
    }

    /**
     * Check if the call was successfully completed.
     *
     * @return bool True if call status is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the call failed to connect.
     *
     * @return bool True if call failed, was busy, or went unanswered
     */
    public function isFailed(): bool
    {
        return in_array($this->status, [
            self::STATUS_FAILED,
            self::STATUS_BUSY,
            self::STATUS_NO_ANSWER,
        ]);
    }

    /**
     * Get the total duration of the call in a human-readable format.
     *
     * @return string Formatted duration (e.g., "1m 30s")
     */
    public function getFormattedDuration(): string
    {
        $seconds = $this->total_duration_seconds ?? 0;

        if ($seconds === 0) {
            return '0s';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes === 0) {
            return "{$remainingSeconds}s";
        }

        return "{$minutes}m {$remainingSeconds}s";
    }
}
