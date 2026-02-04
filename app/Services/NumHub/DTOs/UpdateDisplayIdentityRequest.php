<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Update Display Identity Request DTO.
 *
 * Used for PUT /api/v1/applications/updatedisplayidentity
 * Updates an existing display identity's caller name, logo, etc.
 */
final class UpdateDisplayIdentityRequest extends BaseDTO
{
    /**
     * NumHub entity UUID.
     */
    public ?string $numhubEntityId = null;

    /**
     * NumHub identity UUID.
     */
    public ?string $numhubIdentityId = null;

    /**
     * Caller name displayed on recipient's device.
     */
    public ?string $callerName = null;

    /**
     * Directory ID (max 36 characters).
     */
    public ?string $dirId = null;

    /**
     * URL to logo image.
     */
    public ?string $logoUrl = null;

    /**
     * Call reason text.
     */
    public ?string $callReason = null;

    /**
     * Identity status.
     */
    public ?string $status = null;

    /**
     * Whether this is a deactivation request.
     */
    public bool $isDeactivation = false;

    /**
     * Phone numbers to add.
     *
     * @var array<int, string>|null
     */
    public ?array $tnAdded = null;

    /**
     * Phone numbers to remove.
     *
     * @var array<int, string>|null
     */
    public ?array $tnRemoved = null;

    /**
     * Path to additional TNs file.
     */
    public ?string $additionalTnsPath = null;

    /**
     * Fee acknowledgement.
     */
    public bool $isFeeChecked = false;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->numhubEntityId = $data['numhubEntityId'] ?? null;
        $instance->numhubIdentityId = $data['numhubIdentityId'] ?? null;
        $instance->callerName = $data['callerName'] ?? null;
        $instance->dirId = $data['dirId'] ?? null;
        $instance->logoUrl = $data['logoUrl'] ?? null;
        $instance->callReason = $data['callReason'] ?? null;
        $instance->status = $data['status'] ?? null;
        $instance->isDeactivation = (bool) ($data['isDeactivation'] ?? false);
        $instance->tnAdded = $data['tnAdded'] ?? null;
        $instance->tnRemoved = $data['tnRemoved'] ?? null;
        $instance->additionalTnsPath = $data['additionalTnsPath'] ?? null;
        $instance->isFeeChecked = (bool) ($data['isFeeChecked'] ?? false);

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public static function validationRules(): array
    {
        return [
            'numhubEntityId' => ['required', 'uuid'],
            'numhubIdentityId' => ['required', 'uuid'],
            'callerName' => ['nullable', 'string', 'min:15', 'max:32'],
            'dirId' => ['nullable', 'string', 'max:36'],
            'logoUrl' => ['nullable', 'url', 'max:2048'],
            'callReason' => ['nullable', 'string', 'max:255'],
            'tnAdded.*' => ['string', 'regex:/^\+?1?\d{10}$/'],
            'tnRemoved.*' => ['string', 'regex:/^\+?1?\d{10}$/'],
        ];
    }

    /**
     * Add phone numbers to be added.
     *
     * @param  array<int, string>  $phoneNumbers  Phone numbers to add
     * @return self
     */
    public function addPhoneNumbers(array $phoneNumbers): self
    {
        if ($this->tnAdded === null) {
            $this->tnAdded = [];
        }

        $this->tnAdded = array_merge($this->tnAdded, $phoneNumbers);

        return $this;
    }

    /**
     * Remove phone numbers.
     *
     * @param  array<int, string>  $phoneNumbers  Phone numbers to remove
     * @return self
     */
    public function removePhoneNumbers(array $phoneNumbers): self
    {
        if ($this->tnRemoved === null) {
            $this->tnRemoved = [];
        }

        $this->tnRemoved = array_merge($this->tnRemoved, $phoneNumbers);

        return $this;
    }

    /**
     * Mark as deactivation request.
     *
     * @return self
     */
    public function deactivate(): self
    {
        $this->isDeactivation = true;

        return $this;
    }
}
