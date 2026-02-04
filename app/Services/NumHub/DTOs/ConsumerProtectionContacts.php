<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Consumer Protection Contacts section of a BCID application.
 *
 * Contains contact information for consumer complaints and privacy.
 */
final class ConsumerProtectionContacts extends BaseDTO
{
    /**
     * Email address for consumer complaints.
     */
    public ?string $consumerComplaintEmail = null;

    /**
     * Phone number for consumer complaints.
     */
    public ?string $consumerComplaintPhoneNumber = null;

    /**
     * URL to data privacy policy.
     */
    public ?string $dataPrivacyWebsiteUrl = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->consumerComplaintEmail = $data['consumerComplaintEmail'] ?? null;
        $instance->consumerComplaintPhoneNumber = $data['consumerComplaintPhoneNumber'] ?? null;
        $instance->dataPrivacyWebsiteUrl = $data['dataPrivacyWebsiteUrl'] ?? null;

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public static function validationRules(): array
    {
        return [
            'consumerComplaintEmail' => ['required', 'email', 'max:255'],
            'consumerComplaintPhoneNumber' => ['required', 'string', 'regex:/^\+?[\d\-\s()]+$/'],
            'dataPrivacyWebsiteUrl' => ['nullable', 'url', 'max:255'],
        ];
    }
}
