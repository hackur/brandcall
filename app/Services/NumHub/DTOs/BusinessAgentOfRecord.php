<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Business Agent of Record section of a BCID application.
 *
 * Contains information about the legal representative or agent.
 */
final class BusinessAgentOfRecord extends BaseDTO
{
    /**
     * Institution or firm name.
     */
    public ?string $institutionName = null;

    /**
     * Street address.
     */
    public ?string $streetAddress = null;

    /**
     * City.
     */
    public ?string $city = null;

    /**
     * State code.
     */
    public ?string $state = null;

    /**
     * ZIP/postal code.
     */
    public ?string $zipCode = null;

    /**
     * Contact person name.
     */
    public ?string $contactName = null;

    /**
     * Contact phone number.
     */
    public ?string $phoneNumber = null;

    /**
     * Contact email address.
     */
    public ?string $emailAddress = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->institutionName = $data['institutionName'] ?? null;
        $instance->streetAddress = $data['streetAddress'] ?? null;
        $instance->city = $data['city'] ?? null;
        $instance->state = $data['state'] ?? null;
        $instance->zipCode = $data['zipCode'] ?? null;
        $instance->contactName = $data['contactName'] ?? null;
        $instance->phoneNumber = $data['phoneNumber'] ?? null;
        $instance->emailAddress = $data['emailAddress'] ?? null;

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public static function validationRules(): array
    {
        return [
            'institutionName' => ['nullable', 'string', 'max:255'],
            'streetAddress' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'size:2'],
            'zipCode' => ['nullable', 'string', 'regex:/^\d{5}(-\d{4})?$/'],
            'contactName' => ['nullable', 'string', 'max:100'],
            'phoneNumber' => ['nullable', 'string', 'regex:/^\+?[\d\-\s()]+$/'],
            'emailAddress' => ['nullable', 'email', 'max:255'],
        ];
    }

    /**
     * Get formatted full address.
     *
     * @return string
     */
    public function getFormattedAddress(): string
    {
        return sprintf(
            '%s, %s, %s %s',
            $this->streetAddress ?? '',
            $this->city ?? '',
            $this->state ?? '',
            $this->zipCode ?? ''
        );
    }
}
