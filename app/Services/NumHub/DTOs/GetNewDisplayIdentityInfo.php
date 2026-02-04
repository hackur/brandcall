<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

use DateTimeImmutable;

/**
 * Get New Display Identity Info - Individual identity in response.
 *
 * Contains full display identity metadata including status and dates.
 */
final class GetNewDisplayIdentityInfo extends BaseDTO
{
    /**
     * NumHub identity UUID.
     */
    public ?string $numhubIdentityId = null;

    /**
     * Directory ID.
     */
    public ?string $dirId = null;

    /**
     * Associated phone numbers.
     *
     * @var array<int, string>|null
     */
    public ?array $phoneNumbers = null;

    /**
     * Caller name displayed on recipient's device.
     */
    public ?string $callerName = null;

    /**
     * URL to logo image.
     */
    public ?string $logoUrl = null;

    /**
     * Logo document UUID.
     */
    public ?string $logoId = null;

    /**
     * Call reason text.
     */
    public ?string $callReason = null;

    /**
     * Identity status.
     */
    public ?string $status = null;

    /**
     * Creation timestamp.
     */
    public ?DateTimeImmutable $createdDate = null;

    /**
     * Last update timestamp.
     */
    public ?DateTimeImmutable $updatedDate = null;

    /**
     * Updated by.
     */
    public ?string $updatedBy = null;

    /**
     * Activation timestamp.
     */
    public ?DateTimeImmutable $activationDate = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->numhubIdentityId = $data['numhubIdentityId'] ?? null;
        $instance->dirId = $data['dirId'] ?? null;
        $instance->phoneNumbers = $data['phoneNumbers'] ?? null;
        $instance->callerName = $data['callerName'] ?? null;
        $instance->logoUrl = $data['logoUrl'] ?? null;
        $instance->logoId = $data['logoId'] ?? null;
        $instance->callReason = $data['callReason'] ?? null;
        $instance->status = $data['status'] ?? null;
        $instance->updatedBy = $data['updatedBy'] ?? null;

        if (! empty($data['createdDate'])) {
            $instance->createdDate = new DateTimeImmutable($data['createdDate']);
        }
        if (! empty($data['updatedDate'])) {
            $instance->updatedDate = new DateTimeImmutable($data['updatedDate']);
        }
        if (! empty($data['activationDate'])) {
            $instance->activationDate = new DateTimeImmutable($data['activationDate']);
        }

        return $instance;
    }

    /**
     * Check if this identity is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'Active' || $this->status === 'Complete';
    }

    /**
     * Get phone number count.
     *
     * @return int
     */
    public function getPhoneCount(): int
    {
        return count($this->phoneNumbers ?? []);
    }
}
