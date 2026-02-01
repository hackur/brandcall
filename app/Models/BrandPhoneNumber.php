<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BrandPhoneNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'phone_number',
        'country_code',
        'loa_document_path',
        'loa_verified_at',
        'ownership_status',
        'cnam_display_name',
        'cnam_registered',
        'cnam_registered_at',
        'fcr_registered',
        'fcr_registered_at',
        'analytics_registrations',
        'status',
    ];

    protected $casts = [
        'loa_verified_at' => 'date',
        'cnam_registered' => 'boolean',
        'cnam_registered_at' => 'datetime',
        'fcr_registered' => 'boolean',
        'fcr_registered_at' => 'datetime',
        'analytics_registrations' => 'array',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }
}
