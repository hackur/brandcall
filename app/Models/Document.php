<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    public const TYPE_KYC = 'kyc';
    public const TYPE_BUSINESS_LICENSE = 'business_license';
    public const TYPE_TAX_ID = 'tax_id';
    public const TYPE_LOA = 'loa'; // Letter of Authorization
    public const TYPE_DRIVERS_LICENSE = 'drivers_license';
    public const TYPE_GOVERNMENT_ID = 'government_id';
    public const TYPE_ARTICLES_OF_INCORPORATION = 'articles_incorporation';
    public const TYPE_UTILITY_BILL = 'utility_bill';
    public const TYPE_W9 = 'w9';
    public const TYPE_OTHER = 'other';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'tenant_id',
        'type',
        'name',
        'original_filename',
        'path',
        'mime_type',
        'size',
        'status',
        'notes',
        'reviewed_at',
        'reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'size' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function getDownloadUrl(): string
    {
        return route('documents.download', $this);
    }

    public static function getTypeLabel(string $type): string
    {
        return match ($type) {
            self::TYPE_KYC => 'KYC Document',
            self::TYPE_BUSINESS_LICENSE => 'Business License',
            self::TYPE_TAX_ID => 'Tax ID / EIN',
            self::TYPE_LOA => 'Letter of Authorization',
            self::TYPE_DRIVERS_LICENSE => 'Driver\'s License',
            self::TYPE_GOVERNMENT_ID => 'Government ID',
            self::TYPE_ARTICLES_OF_INCORPORATION => 'Articles of Incorporation',
            self::TYPE_UTILITY_BILL => 'Utility Bill / Bank Statement',
            self::TYPE_W9 => 'W-9 Form',
            self::TYPE_OTHER => 'Other',
            default => $type,
        };
    }

    public static function getAllTypes(): array
    {
        return [
            ['value' => self::TYPE_BUSINESS_LICENSE, 'label' => 'Business License'],
            ['value' => self::TYPE_TAX_ID, 'label' => 'Tax ID / EIN Document'],
            ['value' => self::TYPE_DRIVERS_LICENSE, 'label' => 'Driver\'s License'],
            ['value' => self::TYPE_GOVERNMENT_ID, 'label' => 'Government ID (Passport, State ID)'],
            ['value' => self::TYPE_LOA, 'label' => 'Letter of Authorization'],
            ['value' => self::TYPE_ARTICLES_OF_INCORPORATION, 'label' => 'Articles of Incorporation'],
            ['value' => self::TYPE_UTILITY_BILL, 'label' => 'Utility Bill / Bank Statement'],
            ['value' => self::TYPE_W9, 'label' => 'W-9 Form'],
            ['value' => self::TYPE_KYC, 'label' => 'Other KYC Document'],
            ['value' => self::TYPE_OTHER, 'label' => 'Other'],
        ];
    }
}
