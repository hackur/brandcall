<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Pagination DTO for NumHub API responses.
 *
 * Standard pagination metadata included in list responses.
 */
final class PaginationDTO extends BaseDTO
{
    /**
     * Items per page.
     */
    public int $pageSize = 10;

    /**
     * Current page number (1-indexed).
     */
    public int $currentPage = 1;

    /**
     * Total number of items.
     */
    public int $totalCount = 0;

    /**
     * Total number of pages.
     */
    public int $totalPages = 0;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->pageSize = (int) ($data['pageSize'] ?? 10);
        $instance->currentPage = (int) ($data['currentPage'] ?? 1);
        $instance->totalCount = (int) ($data['totalCount'] ?? 0);
        $instance->totalPages = (int) ($data['totalPages'] ?? 0);

        return $instance;
    }

    /**
     * Check if there are more pages.
     *
     * @return bool
     */
    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    /**
     * Check if there is a previous page.
     *
     * @return bool
     */
    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * Get the next page number.
     *
     * @return int|null
     */
    public function getNextPage(): ?int
    {
        return $this->hasNextPage() ? $this->currentPage + 1 : null;
    }

    /**
     * Get the previous page number.
     *
     * @return int|null
     */
    public function getPreviousPage(): ?int
    {
        return $this->hasPreviousPage() ? $this->currentPage - 1 : null;
    }

    /**
     * Get offset for current page.
     *
     * @return int
     */
    public function getOffset(): int
    {
        return ($this->currentPage - 1) * $this->pageSize;
    }
}
