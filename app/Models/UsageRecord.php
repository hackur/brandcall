<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'year',
        'month',
        'call_count',
        'successful_calls',
        'failed_calls',
        'spam_flagged_calls',
        'total_cost',
        'tier_price',
        'stripe_usage_record_id',
        'synced_to_stripe',
    ];

    protected $casts = [
        'call_count' => 'integer',
        'successful_calls' => 'integer',
        'failed_calls' => 'integer',
        'spam_flagged_calls' => 'integer',
        'total_cost' => 'decimal:2',
        'tier_price' => 'decimal:4',
        'synced_to_stripe' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
