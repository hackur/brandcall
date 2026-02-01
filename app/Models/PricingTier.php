<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_calls',
        'max_calls',
        'price_per_call',
        'order',
        'active',
    ];

    protected $casts = [
        'min_calls' => 'integer',
        'max_calls' => 'integer',
        'price_per_call' => 'decimal:4',
        'order' => 'integer',
        'active' => 'boolean',
    ];

    /**
     * Get the tier for a given call count.
     */
    public static function getTierForCallCount(int $callCount): ?self
    {
        return static::where('active', true)
            ->where('min_calls', '<=', $callCount)
            ->where(function ($query) use ($callCount) {
                $query->whereNull('max_calls')
                    ->orWhere('max_calls', '>=', $callCount);
            })
            ->orderBy('order')
            ->first();
    }

    /**
     * Get the price per call for a given call count.
     */
    public static function getPriceForCallCount(int $callCount): float
    {
        $tier = static::getTierForCallCount($callCount);

        return $tier ? (float) $tier->price_per_call : 0.0750; // Default to highest tier
    }
}
