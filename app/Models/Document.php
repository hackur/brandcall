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
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Document Model - KYC and verification document storage.
 *
 * Handles the storage and management of verification documents uploaded
 * by users during the KYC (Know Your Customer) process. Documents are
 * required for compliance with telecommunications regulations.
 *
 * Document Lifecycle:
 * 1. User uploads document (status: pending)
 * 2. Admin reviews document
 * 3. Admin approves or rejects with notes
 * 4. If all required docs approved, user's KYC is complete
 *
 * File Storage:
 * - Uses Spatie Media Library for file management
 * - Thumbnails generated automatically for images
 * - Files stored in private disk (not publicly accessible)
 * - Type-specific MIME validation enforced
 *
 * @property int $id Primary key
 * @property int $user_id Owning user
 * @property int|null $tenant_id Associated tenant
 * @property string $type Document type constant
 * @property string $name User-provided document name
 * @property string $original_filename Original uploaded filename
 * @property string $path Storage path (legacy, use Media Library)
 * @property string $mime_type MIME type of uploaded file
 * @property int $size File size in bytes
 * @property string $status Review status (pending|approved|rejected)
 * @property string|null $notes Admin review notes (visible to user)
 * @property \Carbon\Carbon|null $reviewed_at When document was reviewed
 * @property int|null $reviewed_by Admin who reviewed
 * @property \Carbon\Carbon|null $last_viewed_at Last preview/download
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read User $user Document owner
 * @property-read Tenant|null $tenant Associated tenant
 * @property-read User|null $reviewer Admin who reviewed
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection $media
 */
class Document extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /*
    |--------------------------------------------------------------------------
    | Document Type Constants
    |--------------------------------------------------------------------------
    |
    | Document types categorize uploads for KYC verification.
    | Different types have different allowed file formats.
    |
    */

    /** @var string General KYC document */
    public const TYPE_KYC = 'kyc';

    /** @var string Business license or permit */
    public const TYPE_BUSINESS_LICENSE = 'business_license';

    /** @var string Tax identification document (EIN, etc.) */
    public const TYPE_TAX_ID = 'tax_id';

    /** @var string Letter of Authorization for phone numbers */
    public const TYPE_LOA = 'loa';

    /** @var string Driver's license for identity verification */
    public const TYPE_DRIVERS_LICENSE = 'drivers_license';

    /** @var string Government-issued ID (passport, state ID) */
    public const TYPE_GOVERNMENT_ID = 'government_id';

    /** @var string Articles of Incorporation */
    public const TYPE_ARTICLES_OF_INCORPORATION = 'articles_incorporation';

    /** @var string Utility bill for address verification */
    public const TYPE_UTILITY_BILL = 'utility_bill';

    /** @var string IRS W-9 form */
    public const TYPE_W9 = 'w9';

    /** @var string Other document type */
    public const TYPE_OTHER = 'other';

    /*
    |--------------------------------------------------------------------------
    | Document Status Constants
    |--------------------------------------------------------------------------
    */

    /** @var string Awaiting admin review */
    public const STATUS_PENDING = 'pending';

    /** @var string Approved by admin */
    public const STATUS_APPROVED = 'approved';

    /** @var string Rejected by admin (see notes for reason) */
    public const STATUS_REJECTED = 'rejected';

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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'last_viewed_at' => 'datetime',
            'size' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Media Library Configuration
    |--------------------------------------------------------------------------
    */

    /**
     * Register media collections with MIME type validation.
     *
     * Documents are stored in a single-file collection. Accepted MIME types
     * vary by document type (e.g., legal docs require PDF).
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('document')
            ->singleFile()
            ->acceptsMimeTypes($this->getAllowedMimeTypes());
    }

    /**
     * Register media conversions for thumbnails and previews.
     *
     * Generates optimized versions of image uploads:
     * - thumb: 200x200 for list views
     * - preview: 800x800 for detail views
     *
     * @param Media|null $media The media item being converted
     * @return void
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

    /*
    |--------------------------------------------------------------------------
    | File Type Validation
    |--------------------------------------------------------------------------
    */

    /**
     * Get allowed MIME types based on document type.
     *
     * Validation rules by document type:
     * - Legal documents (LOA, W-9, Articles): PDF only
     * - ID documents: PDF or images
     * - Business documents: PDF or images
     *
     * @return array<int, string> Array of allowed MIME types
     */
    public function getAllowedMimeTypes(): array
    {
        return match ($this->type) {
            // Legal documents - PDF only for legal validity
            self::TYPE_LOA,
            self::TYPE_ARTICLES_OF_INCORPORATION,
            self::TYPE_W9 => [
                'application/pdf',
            ],

            // ID documents - images or PDF scans
            self::TYPE_DRIVERS_LICENSE,
            self::TYPE_GOVERNMENT_ID => [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/webp',
            ],

            // Business documents - flexible formats
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
     * Get allowed file extensions for a document type.
     *
     * Used for frontend validation hints and error messages.
     *
     * @param string $type Document type constant
     * @return array<int, string> Array of allowed extensions
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
     * Get Laravel validation rules for a document type.
     *
     * Returns validation rules suitable for use with Request validation.
     *
     * @param string $type Document type constant
     * @return array<string, string> Validation rules array
     */
    public static function getValidationRulesForType(string $type): array
    {
        $extensions = self::getAllowedExtensionsForType($type);
        $mimes = implode(',', $extensions);

        return [
            'file' => "required|file|mimes:{$mimes}|max:10240",
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user who uploaded this document.
     *
     * @return BelongsTo<User, Document>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tenant this document belongs to.
     *
     * @return BelongsTo<Tenant, Document>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the admin who reviewed this document.
     *
     * @return BelongsTo<User, Document>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Status Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the document is pending review.
     *
     * @return bool True if status is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the document has been approved.
     *
     * @return bool True if status is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Mark the document as viewed and update timestamp.
     *
     * Called when a user or admin previews/downloads the document.
     * Useful for audit trails and activity tracking.
     *
     * @return void
     */
    public function markAsViewed(): void
    {
        $this->update(['last_viewed_at' => now()]);
    }

    /*
    |--------------------------------------------------------------------------
    | File Access Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get the primary media file from the collection.
     *
     * @return Media|null The media model or null if no file
     */
    public function getFile(): ?Media
    {
        return $this->getFirstMedia('document');
    }

    /**
     * Get the thumbnail URL for list views.
     *
     * Returns null for PDFs (use PDF icon in UI instead).
     *
     * @return string|null Thumbnail URL or null
     */
    public function getThumbnailUrl(): ?string
    {
        $media = $this->getFile();
        if (!$media) {
            return null;
        }

        if ($media->mime_type === 'application/pdf') {
            return null;
        }

        return $media->getUrl('thumb');
    }

    /**
     * Get the preview URL for detail views.
     *
     * For PDFs, returns the full file URL for iframe embedding.
     * For images, returns the optimized preview conversion.
     *
     * @return string|null Preview URL or null
     */
    public function getPreviewUrl(): ?string
    {
        $media = $this->getFile();
        if (!$media) {
            return null;
        }

        if ($media->mime_type === 'application/pdf') {
            return $media->getUrl();
        }

        return $media->getUrl('preview');
    }

    /**
     * Get the download URL for the document.
     *
     * Falls back to legacy path-based route if Media Library
     * file is not available.
     *
     * @return string Download URL
     */
    public function getDownloadUrl(): string
    {
        $media = $this->getFile();
        if ($media) {
            return $media->getUrl();
        }

        return route('filament.admin.documents.download', $this);
    }

    /**
     * Get comprehensive file metadata.
     *
     * Returns all file information in a structured array
     * suitable for API responses or frontend display.
     *
     * @return array<string, mixed> Metadata array
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
     * Format file size for human-readable display.
     *
     * @param int $bytes File size in bytes
     * @return string Formatted size (e.g., "1.5 MB")
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

    /*
    |--------------------------------------------------------------------------
    | Static Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get human-readable label for a document type.
     *
     * @param string $type Document type constant
     * @return string Human-readable label
     */
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

    /**
     * Get all document types with metadata.
     *
     * Returns array suitable for frontend select dropdowns,
     * including allowed extensions for each type.
     *
     * @return array<int, array{value: string, label: string, extensions: array<int, string>}>
     */
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
