<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallLog extends Model
{
    use HasFactory;

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

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (CallLog $log) {
            if (!$log->tenant_id && app()->bound('current_tenant')) {
                $log->tenant_id = app('current_tenant')->id;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function phoneNumber(): BelongsTo
    {
        return $this->belongsTo(BrandPhoneNumber::class, 'brand_phone_number_id');
    }
}
