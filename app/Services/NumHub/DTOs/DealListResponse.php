<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Deal List Response DTO.
 *
 * Returned from GET /api/v1/deals
 * Contains paginated list of deals.
 */
final class DealListResponse extends BaseDTO
{
    /**
     * List of deals.
     *
     * @var array<int, DealResponseDTO>|null
     */
    public ?array $dealsList = null;

    /**
     * Pagination metadata.
     */
    public ?PaginationDTO $metaData = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        if (isset($data['dealsList']) && is_array($data['dealsList'])) {
            $instance->dealsList = array_map(
                fn (array $deal) => DealResponseDTO::fromArray($deal),
                $data['dealsList']
            );
        }

        if (isset($data['metaData']) && is_array($data['metaData'])) {
            $instance->metaData = PaginationDTO::fromArray($data['metaData']);
        }

        return $instance;
    }

    /**
     * Get total deal count.
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->metaData?->totalCount ?? count($this->dealsList ?? []);
    }

    /**
     * Check if there are more pages.
     *
     * @return bool
     */
    public function hasMorePages(): bool
    {
        if ($this->metaData === null) {
            return false;
        }

        return $this->metaData->currentPage < $this->metaData->totalPages;
    }

    /**
     * Get enabled deals only.
     *
     * @return array<int, DealResponseDTO>
     */
    public function getEnabledDeals(): array
    {
        if ($this->dealsList === null) {
            return [];
        }

        return array_filter(
            $this->dealsList,
            fn (DealResponseDTO $deal) => $deal->enabled
        );
    }
}
