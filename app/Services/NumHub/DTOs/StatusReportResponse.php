<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Status Report Response DTO.
 *
 * Contains application and display identity status counts and summaries.
 */
final class StatusReportResponse extends BaseDTO
{
    /**
     * Application status counts.
     *
     * @var array<int, StatusCountItem>|null
     */
    public ?array $application = null;

    /**
     * Display identity status counts.
     *
     * @var array<int, StatusCountItem>|null
     */
    public ?array $dis = null;

    /**
     * Display identity change request counts by OSP.
     *
     * @var array<int, OspStatusSummary>|null
     */
    public ?array $disChangeRequest = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        if (isset($data['application']) && is_array($data['application'])) {
            $instance->application = array_map(
                fn (array $item) => StatusCountItem::fromArray($item),
                $data['application']
            );
        }

        if (isset($data['dis']) && is_array($data['dis'])) {
            $instance->dis = array_map(
                fn (array $item) => StatusCountItem::fromArray($item),
                $data['dis']
            );
        }

        if (isset($data['disChangeRequest']) && is_array($data['disChangeRequest'])) {
            $instance->disChangeRequest = array_map(
                fn (array $item) => OspStatusSummary::fromArray($item),
                $data['disChangeRequest']
            );
        }

        return $instance;
    }

    /**
     * Get total application count.
     *
     * @return int
     */
    public function getTotalApplications(): int
    {
        if ($this->application === null) {
            return 0;
        }

        return array_reduce(
            $this->application,
            fn (int $carry, StatusCountItem $item) => $carry + $item->count,
            0
        );
    }

    /**
     * Get total display identity count.
     *
     * @return int
     */
    public function getTotalIdentities(): int
    {
        if ($this->dis === null) {
            return 0;
        }

        return array_reduce(
            $this->dis,
            fn (int $carry, StatusCountItem $item) => $carry + $item->count,
            0
        );
    }

    /**
     * Get application count by status.
     *
     * @param  string  $status  Status to filter by
     * @return int
     */
    public function getApplicationCountByStatus(string $status): int
    {
        if ($this->application === null) {
            return 0;
        }

        foreach ($this->application as $item) {
            if ($item->status === $status) {
                return $item->count;
            }
        }

        return 0;
    }

    /**
     * Get pending applications count.
     *
     * @return int
     */
    public function getPendingCount(): int
    {
        return $this->getApplicationCountByStatus('PendingReview')
            + $this->getApplicationCountByStatus('Submitted');
    }

    /**
     * Get approved applications count.
     *
     * @return int
     */
    public function getApprovedCount(): int
    {
        return $this->getApplicationCountByStatus('Complete');
    }
}
