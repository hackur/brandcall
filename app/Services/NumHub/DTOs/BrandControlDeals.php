<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

use DateTimeImmutable;

/**
 * BrandControl Deals DTO.
 *
 * Used for POST /api/v1/deals and PUT /api/v1/deals/{dealId}
 * Creates or updates a deal/enterprise in the NumHub system.
 *
 * RoleType options:
 * - "Enterprise" - Enterprise customer
 * - "BPO" - Business Process Outsourcer
 *
 * OrgType options:
 * - "CommercialEnterprise" - Commercial business
 * - "GovtPublicService" - Government/public service
 * - "CharityNonProfit" - Charity/non-profit
 */
final class BrandControlDeals extends BaseDTO
{
    public const ROLE_ENTERPRISE = 'Enterprise';

    public const ROLE_BPO = 'BPO';

    public const ORG_COMMERCIAL = 'CommercialEnterprise';

    public const ORG_GOVERNMENT = 'GovtPublicService';

    public const ORG_NONPROFIT = 'CharityNonProfit';

    /**
     * Client ID.
     */
    public int $clientId = 0;

    /**
     * Deal ID (for updates).
     */
    public int $dealID = 0;

    /**
     * Deal name.
     */
    public ?string $dealName = null;

    /**
     * NumHub internal identifier.
     */
    public ?string $numhubInternalId = null;

    /**
     * Vetting fee amount.
     */
    public float $vettingFee = 0.0;

    /**
     * Retail fee per confirmed delivery.
     */
    public float $retailFee = 0.0;

    /**
     * Platform fee per identity.
     */
    public float $platformFee = 0.0;

    /**
     * Expected monthly call volume.
     */
    public float $callVolume = 0.0;

    /**
     * Customer email address.
     */
    public ?string $customerEmailAddress = null;

    /**
     * Operations email address.
     */
    public ?string $operationsEmail = null;

    /**
     * Customer primary contact first name.
     */
    public ?string $customerPrimaryContactFirstName = null;

    /**
     * Customer primary contact last name.
     */
    public ?string $customerPrimaryContactLastName = null;

    /**
     * Customer phone number.
     */
    public ?string $customerPhoneNumber = null;

    /**
     * Customer street address.
     */
    public ?string $customerAddress = null;

    /**
     * Customer city.
     */
    public ?string $customerCity = null;

    /**
     * Customer state.
     */
    public ?string $customerState = null;

    /**
     * Customer ZIP code.
     */
    public ?string $customerZipCode = null;

    /**
     * Parent ID (for hierarchy).
     */
    public ?string $parentId = null;

    /**
     * Service provider name.
     */
    public ?string $serviceProvider = null;

    /**
     * OSP identifier.
     */
    public ?string $ospId = null;

    /**
     * Notification email addresses (comma-separated).
     */
    public ?string $bcidEidNotificationEmails = null;

    /**
     * Customer last login timestamp.
     */
    public ?DateTimeImmutable $customerLastLoginDate = null;

    /**
     * Responsible Organization ID.
     */
    public ?string $respOrgId = null;

    /**
     * Role type: Enterprise or BPO.
     */
    public ?string $roleType = null;

    /**
     * Organization type.
     */
    public ?string $orgType = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->clientId = (int) ($data['clientId'] ?? 0);
        $instance->dealID = (int) ($data['dealID'] ?? $data['dealId'] ?? 0);
        $instance->dealName = $data['dealName'] ?? null;
        $instance->numhubInternalId = $data['numhubInternalId'] ?? null;
        $instance->vettingFee = (float) ($data['vettingFee'] ?? 0.0);
        $instance->retailFee = (float) ($data['retailFee'] ?? 0.0);
        $instance->platformFee = (float) ($data['platformFee'] ?? 0.0);
        $instance->callVolume = (float) ($data['callVolume'] ?? 0.0);
        $instance->customerEmailAddress = $data['customerEmailAddress'] ?? null;
        $instance->operationsEmail = $data['operationsEmail'] ?? null;
        $instance->customerPrimaryContactFirstName = $data['customerPrimaryContactFirstName'] ?? null;
        $instance->customerPrimaryContactLastName = $data['customerPrimaryContactLastName'] ?? null;
        $instance->customerPhoneNumber = $data['customerPhoneNumber'] ?? null;
        $instance->customerAddress = $data['customerAddress'] ?? null;
        $instance->customerCity = $data['customerCity'] ?? null;
        $instance->customerState = $data['customerState'] ?? null;
        $instance->customerZipCode = $data['customerZipCode'] ?? null;
        $instance->parentId = $data['parentId'] ?? null;
        $instance->serviceProvider = $data['serviceProvider'] ?? null;
        $instance->ospId = $data['ospId'] ?? null;
        $instance->bcidEidNotificationEmails = $data['bcidEidNotificationEmails'] ?? null;
        $instance->respOrgId = $data['respOrgId'] ?? null;
        $instance->roleType = $data['roleType'] ?? null;
        $instance->orgType = $data['orgType'] ?? null;

        if (! empty($data['customerLastLoginDate'])) {
            $instance->customerLastLoginDate = new DateTimeImmutable($data['customerLastLoginDate']);
        }

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public static function validationRules(): array
    {
        return [
            'dealName' => ['required', 'string', 'max:255'],
            'customerEmailAddress' => ['required', 'email', 'max:255'],
            'customerPrimaryContactFirstName' => ['required', 'string', 'max:50'],
            'customerPrimaryContactLastName' => ['required', 'string', 'max:50'],
            'customerPhoneNumber' => ['required', 'string', 'regex:/^\+?[\d\-\s()]+$/'],
            'customerAddress' => ['required', 'string', 'max:255'],
            'customerCity' => ['required', 'string', 'max:100'],
            'customerState' => ['required', 'string', 'size:2'],
            'customerZipCode' => ['required', 'string', 'regex:/^\d{5}(-\d{4})?$/'],
            'roleType' => ['required', 'in:Enterprise,BPO'],
            'orgType' => ['required', 'in:CommercialEnterprise,GovtPublicService,CharityNonProfit'],
        ];
    }

    /**
     * Get customer's full name.
     *
     * @return string
     */
    public function getCustomerFullName(): string
    {
        return trim(sprintf(
            '%s %s',
            $this->customerPrimaryContactFirstName ?? '',
            $this->customerPrimaryContactLastName ?? ''
        ));
    }

    /**
     * Get formatted address.
     *
     * @return string
     */
    public function getFormattedAddress(): string
    {
        return sprintf(
            '%s, %s, %s %s',
            $this->customerAddress ?? '',
            $this->customerCity ?? '',
            $this->customerState ?? '',
            $this->customerZipCode ?? ''
        );
    }
}
