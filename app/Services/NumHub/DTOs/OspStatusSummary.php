<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * OSP Status Summary DTO.
 *
 * Summarizes status counts by OSP, used in status reports.
 */
final class OspStatusSummary extends BaseDTO
{
    /**
     * Status name.
     */
    public ?string $status = null;

    /**
     * OSP names with this status.
     *
     * @var array<int, string>|null
     */
    public ?array $ospNames = null;

    /**
     * Total count.
     */
    public int $count = 0;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->status = $data['status'] ?? null;
        $instance->ospNames = $data['ospNames'] ?? null;
        $instance->count = (int) ($data['count'] ?? 0);

        return $instance;
    }

    /**
     * Get OSP count.
     *
     * @return int
     */
    public function getOspCount(): int
    {
        return count($this->ospNames ?? []);
    }
}
