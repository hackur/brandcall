<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Vetting Report Response DTO.
 *
 * Returned from GET /api/v1/application/vetting-report/{NumhubEntityId}
 * Contains application with vetting review details.
 */
final class VettingReportResponse extends BaseDTO
{
    /**
     * Full application data.
     */
    public ?ApplicationResponse $application = null;

    /**
     * Vetting review details per section.
     *
     * @var array<int, VettingReviewDTO>|null
     */
    public ?array $vettingReview = null;

    /**
     * URL to Middesk PDF verification report.
     */
    public ?string $middeskPdfReport = null;

    /**
     * List of modified section names.
     *
     * @var array<int, string>|null
     */
    public ?array $modifiedSections = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        if (isset($data['application']) && is_array($data['application'])) {
            $instance->application = ApplicationResponse::fromArray($data['application']);
        }

        if (isset($data['vettingReview']) && is_array($data['vettingReview'])) {
            $instance->vettingReview = array_map(
                fn (array $review) => VettingReviewDTO::fromArray($review),
                $data['vettingReview']
            );
        }

        $instance->middeskPdfReport = $data['middeskPdfReport'] ?? null;
        $instance->modifiedSections = $data['modifiedSections'] ?? null;

        return $instance;
    }

    /**
     * Check if any section failed vetting.
     *
     * @return bool
     */
    public function hasFailedSections(): bool
    {
        if ($this->vettingReview === null) {
            return false;
        }

        foreach ($this->vettingReview as $review) {
            if ($review->cxTeamReview === 'Fail') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if any section is flagged.
     *
     * @return bool
     */
    public function hasFlaggedSections(): bool
    {
        if ($this->vettingReview === null) {
            return false;
        }

        foreach ($this->vettingReview as $review) {
            if ($review->cxTeamReview === 'Flag') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get failed section reviews.
     *
     * @return array<int, VettingReviewDTO>
     */
    public function getFailedSections(): array
    {
        if ($this->vettingReview === null) {
            return [];
        }

        return array_filter(
            $this->vettingReview,
            fn (VettingReviewDTO $review) => $review->cxTeamReview === 'Fail'
        );
    }
}
