<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'display_name',
        'logo_path',
        'call_reason',
        'rich_call_data',
        'numhub_enterprise_id',
        'numhub_vetting_status',
        'bcid_trust_product_sid',
        'api_key',
        'api_secret',
        'api_key_last_rotated_at',
        'default_attestation_level',
        'stir_shaken_enabled',
        'status',
        'metadata',
    ];

    protected $casts = [
        'rich_call_data' => 'array',
        'metadata' => 'array',
        'stir_shaken_enabled' => 'boolean',
        'api_key_last_rotated_at' => 'datetime',
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Brand $brand) {
            if (!$brand->tenant_id && app()->bound('current_tenant')) {
                $brand->tenant_id = app('current_tenant')->id;
            }
            
            if (!$brand->api_key) {
                $brand->api_key = 'bci_' . bin2hex(random_bytes(32));
            }
            
            if (!$brand->slug) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(BrandPhoneNumber::class);
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }

    public function rotateApiKey(): void
    {
        $this->update([
            'api_key' => 'bci_' . bin2hex(random_bytes(32)),
            'api_key_last_rotated_at' => now(),
        ]);
    }
}
