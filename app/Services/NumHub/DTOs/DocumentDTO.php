<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

use DateTimeImmutable;

/**
 * Document DTO for NumHub documents.
 *
 * Represents an uploaded document (LOA, business license, etc.)
 */
final class DocumentDTO extends BaseDTO
{
    /**
     * Document UUID.
     */
    public ?string $documentId = null;

    /**
     * File name.
     */
    public ?string $name = null;

    /**
     * Download URL.
     */
    public ?string $url = null;

    /**
     * Upload timestamp.
     */
    public ?DateTimeImmutable $createdDate = null;

    /**
     * Last modification timestamp.
     */
    public ?DateTimeImmutable $modifiedDate = null;

    /**
     * Uploaded by.
     */
    public ?string $createdBy = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->documentId = $data['documentId'] ?? $data['id'] ?? null;
        $instance->name = $data['name'] ?? null;
        $instance->url = $data['url'] ?? null;
        $instance->createdBy = $data['createdBy'] ?? null;

        if (! empty($data['createdDate'])) {
            $instance->createdDate = new DateTimeImmutable($data['createdDate']);
        }
        if (! empty($data['modifiedDate'])) {
            $instance->modifiedDate = new DateTimeImmutable($data['modifiedDate']);
        }

        return $instance;
    }

    /**
     * Get file extension from name.
     *
     * @return string|null
     */
    public function getExtension(): ?string
    {
        if ($this->name === null) {
            return null;
        }

        return strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
    }

    /**
     * Check if this is a PDF document.
     *
     * @return bool
     */
    public function isPdf(): bool
    {
        return $this->getExtension() === 'pdf';
    }
}
