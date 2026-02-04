<?php

declare(strict_types=1);

namespace App\Services\NumHub\Enums;

/**
 * BCID Application status values.
 */
enum ApplicationStatus: string
{
    case PENDING_REVIEW = 'PendingReview';
    case COMPLETE = 'Complete';
    case SAVED = 'Saved';
    case REJECTED = 'Rejected';
    case RESUBMITTED = 'Resubmitted';
    case IN_PROGRESS = 'InProgress';
    case SUBMITTED = 'Submitted';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING_REVIEW => 'Pending Review',
            self::COMPLETE => 'Complete',
            self::SAVED => 'Saved (Draft)',
            self::REJECTED => 'Rejected',
            self::RESUBMITTED => 'Resubmitted',
            self::IN_PROGRESS => 'In Progress',
            self::SUBMITTED => 'Submitted',
        };
    }

    /**
     * Get status color for UI.
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING_REVIEW => 'warning',
            self::COMPLETE => 'success',
            self::SAVED => 'gray',
            self::REJECTED => 'danger',
            self::RESUBMITTED => 'info',
            self::IN_PROGRESS => 'primary',
            self::SUBMITTED => 'info',
        };
    }
}
