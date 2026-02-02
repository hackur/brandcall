<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Document extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public const TYPE_KYC = 'kyc';
    public const TYPE_BUSINESS_LICENSE = 'business_license';
    public const TYPE_TAX_ID = 'tax_id';
    public const TYPE_LOA = 'loa';
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
        'last_viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'last_viewed_at' => 'datetime',
            'size' => 'integer',
        ];
    }

    /**
     * Register media collections with MIME type validation per document type.
     */
    public function registerMediaCollections(): void
    {
        // PDF-only documents (legal/business documents)
        $this->addMediaCollection('document')
            ->singleFile()
            ->acceptsMimeTypes($this->getAllowedMimeTypes());
    }

    /**
     * Register media conversions (thumbnails).
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(800)
            ->sharpen(5)
            ->nonQueued();
    }

    /**
     * Get allowed MIME types based on document type.
     */
    public function getAllowedMimeTypes(): array
    {
        return match ($this->type) {
            // Legal documents - PDF only
            self::TYPE_LOA,
            self::TYPE_ARTICLES_OF_INCORPORATION,
            self::TYPE_W9 => [
                'application/pdf',
            ],

            // ID documents - images or PDF
            self::TYPE_DRIVERS_LICENSE,
            self::TYPE_GOVERNMENT_ID => [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/webp',
            ],

            // Business documents - PDF or images
            self::TYPE_BUSINESS_LICENSE,
            self::TYPE_TAX_ID,
            self::TYPE_UTILITY_BILL,
            self::TYPE_KYC,
            self::TYPE_OTHER => [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif',
            ],

            default => [
                'application/pdf',
                'image/jpeg',
                'image/png',
            ],
        };
    }

    /**
     * Get allowed file extensions for display.
     */
    public static function getAllowedExtensionsForType(string $type): array
    {
        return match ($type) {
            self::TYPE_LOA,
            self::TYPE_ARTICLES_OF_INCORPORATION,
            self::TYPE_W9 => ['pdf'],

            self::TYPE_DRIVERS_LICENSE,
            self::TYPE_GOVERNMENT_ID => ['pdf', 'jpg', 'jpeg', 'png', 'webp'],

            default => ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'gif'],
        };
    }

    /**
     * Get validation rules for a document type.
     */
    public static function getValidationRulesForType(string $type): array
    {
        $extensions = self::getAllowedExtensionsForType($type);
        $mimes = implode(',', $extensions);

        return [
            'file' => "required|file|mimes:{$mimes}|max:10240",
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

    /**
     * Mark document as viewed and update timestamp.
     */
    public function markAsViewed(): void
    {
        $this->update(['last_viewed_at' => now()]);
    }

    /**
     * Get the primary media file.
     */
    public function getFile(): ?Media
    {
        return $this->getFirstMedia('document');
    }

    /**
     * Get thumbnail URL.
     */
    public function getThumbnailUrl(): ?string
    {
        $media = $this->getFile();
        if (!$media) {
            return null;
        }

        // For PDFs, return a placeholder or first page conversion
        if ($media->mime_type === 'application/pdf') {
            return null; // Will use PDF icon in frontend
        }

        return $media->getUrl('thumb');
    }

    /**
     * Get preview URL.
     */
    public function getPreviewUrl(): ?string
    {
        $media = $this->getFile();
        if (!$media) {
            return null;
        }

        if ($media->mime_type === 'application/pdf') {
            return $media->getUrl(); // Return full PDF for preview
        }

        return $media->getUrl('preview');
    }

    /**
     * Get download URL.
     */
    public function getDownloadUrl(): string
    {
        $media = $this->getFile();
        if ($media) {
            return $media->getUrl();
        }

        // Fallback to legacy path-based download
        return route('filament.admin.documents.download', $this);
    }

    /**
     * Get file metadata.
     */
    public function getMetadata(): array
    {
        $media = $this->getFile();

        return [
            'filename' => $this->original_filename,
            'mime_type' => $media?->mime_type ?? $this->mime_type,
            'size' => $media?->size ?? $this->size,
            'size_formatted' => $this->formatFileSize($media?->size ?? $this->size),
            'extension' => pathinfo($this->original_filename, PATHINFO_EXTENSION),
            'uploaded_at' => $this->created_at,
            'modified_at' => $this->updated_at,
            'last_viewed_at' => $this->last_viewed_at,
            'reviewed_at' => $this->reviewed_at,
            'is_image' => str_starts_with($media?->mime_type ?? $this->mime_type, 'image/'),
            'is_pdf' => ($media?->mime_type ?? $this->mime_type) === 'application/pdf',
        ];
    }

    /**
     * Format file size for display.
     */
    protected function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
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
            [
                'value' => self::TYPE_BUSINESS_LICENSE,
                'label' => 'Business License',
                'extensions' => self::getAllowedExtensionsForType(self::TYPE_BUSINESS_LICENSE),
            ],
            [
                'value' => self::TYPE_TAX_ID,
                'label' => 'Tax ID / EIN Document',
                'extensions' => self::getAllowedExtensionsForType(self::TYPE_TAX_ID),
            ],
            [
                'value' => self::TYPE_DRIVERS_LICENSE,
                'label' => 'Driver\'s License',
                'extensions' => self::getAllowedExtensionsForType(self::TYPE_DRIVERS_LICENSE),
            ],
            [
                'value' => self::TYPE_GOVERNMENT_ID,
                'label' => 'Government ID (Passport, State ID)',
                'extensions' => self::getAllowedExtensionsForType(self::TYPE_GOVERNMENT_ID),
            ],
            [
                'value' => self::TYPE_LOA,
                'label' => 'Letter of Authorization (PDF only)',
                'extensions' => self::getAllowedExtensionsForType(self::TYPE_LOA),
            ],
            [
                'value' => self::TYPE_ARTICLES_OF_INCORPORATION,
                'label' => 'Articles of Incorporation (PDF only)',
                'extensions' => self::getAllowedExtensionsForType(self::TYPE_ARTICLES_OF_INCORPORATION),
            ],
            [
                'value' => self::TYPE_UTILITY_BILL,
                'label' => 'Utility Bill / Bank Statement',
                'extensions' => self::getAllowedExtensionsForType(self::TYPE_UTILITY_BILL),
            ],
            [
                'value' => self::TYPE_W9,
                'label' => 'W-9 Form (PDF only)',
                'extensions' => self::getAllowedExtensionsForType(self::TYPE_W9),
            ],
            [
                'value' => self::TYPE_KYC,
                'label' => 'Other KYC Document',
                'extensions' => self::getAllowedExtensionsForType(self::TYPE_KYC),
            ],
            [
                'value' => self::TYPE_OTHER,
                'label' => 'Other',
                'extensions' => self::getAllowedExtensionsForType(self::TYPE_OTHER),
            ],
        ];
    }
}
