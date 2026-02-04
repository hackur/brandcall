<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

use DateTimeImmutable;

/**
 * Display Identity Response DTO.
 *
 * Returned from GET /api/v1/applications/newidentities/{NumhubIdentityId}
 * Contains full display identity details.
 */
final class DisplayIdentityResponse extends BaseDTO
{
    /**
     * Caller name displayed on recipient's device (15-32 chars).
     */
    public ?string $callerName = null;

    /**
     * Call reason text.
     */
    public ?string $callReason = null;

    /**
     * URL to logo image.
     */
    public ?string $logoUrl = null;

    /**
     * Directory ID.
     */
    public ?string $dirId = null;

    /**
     * Activation timestamp.
     */
    public ?DateTimeImmutable $activationDate = null;

    /**
     * Deactivation timestamp.
     */
    public ?DateTimeImmutable $deactivationDate = null;

    /**
     * Last modification timestamp.
     */
    public ?DateTimeImmutable $modifiedDate = null;

    /**
     * Company name.
     */
    public ?string $companyName = null;

    /**
     * Enterprise ID.
     */
    public ?string $eid = null;

    /**
     * NumHub entity UUID.
     */
    public ?string $numhubEntityId = null;

    /**
     * NumHub identity UUID.
     */
    public ?string $numhubIdentityId = null;

    /**
     * Identity status.
     */
    public ?string $status = null;

    /**
     * Path to additional TNs file.
     */
    public ?string $additionalTnsPath = null;

    /**
     * Fee acknowledgement.
     */
    public bool $isFeeChecked = false;

    /**
     * Associated phone numbers.
     *
     * @var array<int, string>|null
     */
    public ?array $phoneNumbers = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->callerName = $data['callerName'] ?? null;
        $instance->callReason = $data['callReason'] ?? null;
        $instance->logoUrl = $data['logoUrl'] ?? null;
        $instance->dirId = $data['dirId'] ?? null;
        $instance->companyName = $data['companyName'] ?? null;
        $instance->eid = $data['eid'] ?? null;
        $instance->numhubEntityId = $data['numhubEntityId'] ?? null;
        $instance->numhubIdentityId = $data['numhubIdentityId'] ?? null;
        $instance->status = $data['status'] ?? null;
        $instance->additionalTnsPath = $data['additionalTnsPath'] ?? null;
        $instance->isFeeChecked = (bool) ($data['isFeeChecked'] ?? false);
        $instance->phoneNumbers = $data['phoneNumbers'] ?? null;

        if (! empty($data['activationDate'])) {
            $instance->activationDate = new DateTimeImmutable($data['activationDate']);
        }
        if (! empty($data['deactivationDate'])) {
            $instance->deactivationDate = new DateTimeImmutable($data['deactivationDate']);
        }
        if (! empty($data['modifiedDate'])) {
            $instance->modifiedDate = new DateTimeImmutable($data['modifiedDate']);
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
     * Check if this identity is deactivated.
     *
     * @return bool
     */
    public function isDeactivated(): bool
    {
        return $this->deactivationDate !== null || $this->status === 'Deactivated';
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
