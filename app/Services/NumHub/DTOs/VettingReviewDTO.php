<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

use DateTimeImmutable;

/**
 * Vetting Review DTO.
 *
 * Contains vetting status and comments for a single application section.
 */
final class VettingReviewDTO extends BaseDTO
{
    /**
     * Review UUID.
     */
    public ?string $id = null;

    /**
     * NumHub entity UUID.
     */
    public ?string $numhubEntityId = null;

    /**
     * Section number (1-8).
     */
    public int $sectionNumber = 0;

    /**
     * Section name.
     */
    public ?string $sectionName = null;

    /**
     * CX team review status: Pass, Fail, Flag.
     */
    public ?string $cxTeamReview = null;

    /**
     * Review comments.
     */
    public ?string $comments = null;

    /**
     * Review creation timestamp.
     */
    public ?DateTimeImmutable $created = null;

    /**
     * Whether this section is verified.
     */
    public bool $isVerified = false;

    /**
     * Supporting documents for this review.
     *
     * @var array<int, DocumentDTO>|null
     */
    public ?array $documents = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->id = $data['id'] ?? null;
        $instance->numhubEntityId = $data['numhubEntityId'] ?? null;
        $instance->sectionNumber = (int) ($data['sectionNumber'] ?? 0);
        $instance->sectionName = $data['sectionName'] ?? null;
        $instance->cxTeamReview = $data['cxTeamReview'] ?? null;
        $instance->comments = $data['comments'] ?? null;
        $instance->isVerified = (bool) ($data['isVerified'] ?? false);

        if (! empty($data['created'])) {
            $instance->created = new DateTimeImmutable($data['created']);
        }

        if (isset($data['documents']) && is_array($data['documents'])) {
            $instance->documents = array_map(
                fn (array $doc) => DocumentDTO::fromArray($doc),
                $data['documents']
            );
        }

        return $instance;
    }

    /**
     * Check if this section passed vetting.
     *
     * @return bool
     */
    public function passed(): bool
    {
        return $this->cxTeamReview === 'Pass';
    }

    /**
     * Check if this section failed vetting.
     *
     * @return bool
     */
    public function failed(): bool
    {
        return $this->cxTeamReview === 'Fail';
    }

    /**
     * Check if this section is flagged.
     *
     * @return bool
     */
    public function isFlagged(): bool
    {
        return $this->cxTeamReview === 'Flag';
    }
}
