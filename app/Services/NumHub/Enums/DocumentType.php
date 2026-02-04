<?php

declare(strict_types=1);

namespace App\Services\NumHub\Enums;

/**
 * NumHub document types for upload.
 */
enum DocumentType: string
{
    case LOA = 'LOA';           // Letter of Authorization - PDF only
    case LOGO = 'LOGO';         // Brand logo - BMP only
    case DOCUMENTS = 'DOCUMENTS'; // Supporting docs - PDF, XLSX, CSV

    /**
     * Get allowed MIME types for this document type.
     *
     * @return array<string>
     */
    public function allowedMimeTypes(): array
    {
        return match ($this) {
            self::LOA => ['application/pdf'],
            self::LOGO => ['image/bmp', 'image/x-bmp'],
            self::DOCUMENTS => [
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv',
            ],
        };
    }

    /**
     * Get allowed file extensions.
     *
     * @return array<string>
     */
    public function allowedExtensions(): array
    {
        return match ($this) {
            self::LOA => ['pdf'],
            self::LOGO => ['bmp'],
            self::DOCUMENTS => ['pdf', 'xlsx', 'csv'],
        };
    }

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::LOA => 'Letter of Authorization',
            self::LOGO => 'Brand Logo',
            self::DOCUMENTS => 'Supporting Documents',
        };
    }
}
