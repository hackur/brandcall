<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Status Count Item DTO.
 *
 * Simple count by status, used in status reports.
 */
final class StatusCountItem extends BaseDTO
{
    /**
     * Status name.
     */
    public ?string $status = null;

    /**
     * Count of items with this status.
     */
    public int $count = 0;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->status = $data['status'] ?? null;
        $instance->count = (int) ($data['count'] ?? 0);

        return $instance;
    }
}
