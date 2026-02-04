<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

use DateTimeImmutable;

/**
 * Deal Response DTO.
 *
 * Individual deal in the DealListResponse.
 */
final class DealResponseDTO extends BaseDTO
{
    /**
     * Client ID.
     */
    public int $clientId = 0;

    /**
     * Deal ID.
     */
    public int $dealId = 0;

    /**
     * Client name.
     */
    public ?string $clientName = null;

    /**
     * Customer email address.
     */
    public ?string $customerEmailAddress = null;

    /**
     * Customer email (alternate field).
     */
    public ?string $customerEmail = null;

    /**
     * Customer last login timestamp.
     */
    public ?DateTimeImmutable $customerLastLoginTimestamp = null;

    /**
     * Whether the deal is enabled.
     */
    public bool $enabled = false;

    /**
     * Last modification timestamp.
     */
    public ?DateTimeImmutable $modifiedDate = null;

    /**
     * Last modified by.
     */
    public ?string $modifiedBy = null;

    /**
     * Responsible Organization ID.
     */
    public ?string $respOrgId = null;

    /**
     * Whether BCID is enabled.
     */
    public bool $hasBCID = false;

    /**
     * BCID service provider.
     */
    public ?string $bcidServiceProvider = null;

    /**
     * BCID parent ID.
     */
    public ?string $bcidParentId = null;

    /**
     * NumHub internal ID.
     */
    public ?string $numhubInternalId = null;

    /**
     * White-labeled URL.
     */
    public ?string $bcidWhiteLabeledUrl = null;

    /**
     * OSP ID.
     */
    public ?string $bcidOspId = null;

    /**
     * Whether this is an OSP account.
     */
    public bool $bcidIsOsp = false;

    /**
     * EID notification emails.
     */
    public ?string $bcidEidNotificationEmails = null;

    /**
     * Role type.
     */
    public ?string $bcidRoleType = null;

    /**
     * Organization type.
     */
    public ?string $bcidOrgType = null;

    /**
     * Full name (computed).
     */
    public ?string $fullName = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->clientId = (int) ($data['clientId'] ?? 0);
        $instance->dealId = (int) ($data['dealId'] ?? 0);
        $instance->clientName = $data['clientName'] ?? null;
        $instance->customerEmailAddress = $data['customerEmailAddress'] ?? null;
        $instance->customerEmail = $data['customerEmail'] ?? null;
        $instance->enabled = (bool) ($data['enabled'] ?? false);
        $instance->modifiedBy = $data['modifiedBy'] ?? null;
        $instance->respOrgId = $data['respOrgId'] ?? null;
        $instance->hasBCID = (bool) ($data['hasBCID'] ?? false);
        $instance->bcidServiceProvider = $data['bcidServiceProvider'] ?? null;
        $instance->bcidParentId = $data['bcidParentId'] ?? null;
        $instance->numhubInternalId = $data['numhubInternalId'] ?? null;
        $instance->bcidWhiteLabeledUrl = $data['bcidWhiteLabeledUrl'] ?? null;
        $instance->bcidOspId = $data['bcidOspId'] ?? null;
        $instance->bcidIsOsp = (bool) ($data['bcidIsOsp'] ?? false);
        $instance->bcidEidNotificationEmails = $data['bcidEidNotificationEmails'] ?? null;
        $instance->bcidRoleType = $data['bcidRoleType'] ?? null;
        $instance->bcidOrgType = $data['bcidOrgType'] ?? null;
        $instance->fullName = $data['fullName'] ?? null;

        if (! empty($data['customerLastLoginTimestamp'])) {
            $instance->customerLastLoginTimestamp = new DateTimeImmutable($data['customerLastLoginTimestamp']);
        }
        if (! empty($data['modifiedDate'])) {
            $instance->modifiedDate = new DateTimeImmutable($data['modifiedDate']);
        }

        return $instance;
    }

    /**
     * Get effective email address.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->customerEmailAddress ?? $this->customerEmail;
    }

    /**
     * Check if this is an enterprise deal.
     *
     * @return bool
     */
    public function isEnterprise(): bool
    {
        return $this->bcidRoleType === 'Enterprise';
    }

    /**
     * Check if this is a BPO deal.
     *
     * @return bool
     */
    public function isBpo(): bool
    {
        return $this->bcidRoleType === 'BPO';
    }
}
