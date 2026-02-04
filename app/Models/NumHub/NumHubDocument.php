<?php

declare(strict_types=1);

namespace App\Models\NumHub;

use App\Models\Document;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * NumHubDocument Model - Document sync tracking.
 *
 * Tracks documents uploaded to NumHub for BCID verification.
 * Links to local documents table when applicable, allowing
 * sync status tracking between BrandCall and NumHub.
 *
 * @property string              $id                 UUID primary key
 * @property string              $numhub_entity_id   Parent entity UUID
 * @property int|null            $local_document_id  Link to documents table
 * @property string|null         $numhub_document_id ID returned from NumHub
 * @property string              $document_type      Type: loa|business_license|etc.
 * @property string              $filename           Original filename
 * @property string              $status             Status: pending|uploaded|verified|rejected
 * @property array|null          $api_response       NumHub's response
 * @property \Carbon\Carbon|null $uploaded_at        When uploaded to NumHub
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 * @property-read NumHubEntity   $entity              Parent entity
 * @property-read Document|null  $localDocument       Local document record
 */
class NumHubDocument extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'numhub_documents';

    /**
     * Document type constants.
     */
    public const TYPE_LOA = 'loa';

    public const TYPE_BUSINESS_LICENSE = 'business_license';

    public const TYPE_TAX_ID = 'tax_id';

    public const TYPE_GOVERNMENT_ID = 'government_id';

    public const TYPE_ARTICLES_OF_INCORPORATION = 'articles_incorporation';

    public const TYPE_OTHER = 'other';

    /**
     * Status constants.
     */
    public const STATUS_PENDING = 'pending';

    public const STATUS_UPLOADED = 'uploaded';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numhub_entity_id',
        'local_document_id',
        'numhub_document_id',
        'document_type',
        'filename',
        'status',
        'api_response',
        'uploaded_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'api_response' => 'array',
            'uploaded_at' => 'datetime',
        ];
    }

    /**
     * Get the entity that owns this document.
     *
     * @return BelongsTo<NumHubEntity, NumHubDocument>
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(NumHubEntity::class, 'numhub_entity_id');
    }

    /**
     * Get the local document record.
     *
     * @return BelongsTo<Document, NumHubDocument>
     */
    public function localDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'local_document_id');
    }

    /**
     * Check if the document is pending upload.
     *
     * @return bool True if pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the document has been uploaded to NumHub.
     *
     * @return bool True if uploaded
     */
    public function isUploaded(): bool
    {
        return in_array($this->status, [
            self::STATUS_UPLOADED,
            self::STATUS_VERIFIED,
        ], true);
    }

    /**
     * Check if the document has been verified by NumHub.
     *
     * @return bool True if verified
     */
    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    /**
     * Check if the document was rejected by NumHub.
     *
     * @return bool True if rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Mark as uploaded to NumHub.
     *
     * @param string               $numhubDocumentId Document ID from NumHub
     * @param array<string, mixed> $response         API response
     */
    public function markAsUploaded(string $numhubDocumentId, array $response = []): void
    {
        $this->update([
            'numhub_document_id' => $numhubDocumentId,
            'status' => self::STATUS_UPLOADED,
            'api_response' => $response,
            'uploaded_at' => now(),
        ]);
    }

    /**
     * Mark as verified by NumHub.
     */
    public function markAsVerified(): void
    {
        $this->update(['status' => self::STATUS_VERIFIED]);
    }

    /**
     * Mark as rejected by NumHub.
     *
     * @param string|null $reason Rejection reason
     */
    public function markAsRejected(?string $reason = null): void
    {
        $response = $this->api_response ?? [];
        if ($reason) {
            $response['rejectionReason'] = $reason;
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'api_response' => $response,
        ]);
    }

    /**
     * Create from local document for NumHub upload.
     *
     * @param NumHubEntity $entity   Parent entity
     * @param Document     $document Local document
     *
     * @return self The created NumHub document tracker
     */
    public static function createFromLocal(NumHubEntity $entity, Document $document): self
    {
        return static::create([
            'numhub_entity_id' => $entity->id,
            'local_document_id' => $document->id,
            'document_type' => static::mapLocalType($document->type),
            'filename' => $document->original_filename,
            'status' => self::STATUS_PENDING,
        ]);
    }

    /**
     * Map local document type to NumHub document type.
     *
     * @param string $localType Local document type constant
     *
     * @return string NumHub document type
     */
    protected static function mapLocalType(string $localType): string
    {
        return match ($localType) {
            Document::TYPE_LOA => self::TYPE_LOA,
            Document::TYPE_BUSINESS_LICENSE => self::TYPE_BUSINESS_LICENSE,
            Document::TYPE_TAX_ID => self::TYPE_TAX_ID,
            Document::TYPE_DRIVERS_LICENSE,
            Document::TYPE_GOVERNMENT_ID => self::TYPE_GOVERNMENT_ID,
            Document::TYPE_ARTICLES_OF_INCORPORATION => self::TYPE_ARTICLES_OF_INCORPORATION,
            default => self::TYPE_OTHER,
        };
    }

    /**
     * Get all document types with labels.
     *
     * @return array<string, string>
     */
    public static function getDocumentTypes(): array
    {
        return [
            self::TYPE_LOA => 'Letter of Authorization',
            self::TYPE_BUSINESS_LICENSE => 'Business License',
            self::TYPE_TAX_ID => 'Tax ID / EIN',
            self::TYPE_GOVERNMENT_ID => 'Government ID',
            self::TYPE_ARTICLES_OF_INCORPORATION => 'Articles of Incorporation',
            self::TYPE_OTHER => 'Other',
        ];
    }

    /**
     * Get all statuses with labels.
     *
     * @return array<string, string>
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending Upload',
            self::STATUS_UPLOADED => 'Uploaded',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    /**
     * Scope to pending documents.
     *
     * @param \Illuminate\Database\Eloquent\Builder<NumHubDocument> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<NumHubDocument>
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to uploaded documents.
     *
     * @param \Illuminate\Database\Eloquent\Builder<NumHubDocument> $query
     *
     * @return \Illuminate\Database\Eloquent\Builder<NumHubDocument>
     */
    public function scopeUploaded($query)
    {
        return $query->whereIn('status', [
            self::STATUS_UPLOADED,
            self::STATUS_VERIFIED,
        ]);
    }
}
