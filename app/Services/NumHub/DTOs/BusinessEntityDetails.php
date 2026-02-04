<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Business Entity Details section of a BCID application.
 *
 * Contains company information and address.
 */
final class BusinessEntityDetails extends BaseDTO
{
    /**
     * Legal business name as registered.
     */
    public ?string $legalBusinessName = null;

    /**
     * Company website URL.
     */
    public ?string $businessWebsite = null;

    /**
     * Primary business street address.
     */
    public ?string $primaryBusinessAddress = null;

    /**
     * City.
     */
    public ?string $city = null;

    /**
     * State code (e.g., "CA", "NY").
     */
    public ?string $state = null;

    /**
     * Business phone number.
     */
    public ?string $phone = null;

    /**
     * Originating Service Provider name.
     */
    public ?string $originatingServiceProvider = null;

    /**
     * ZIP/postal code.
     */
    public ?string $zipCode = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->legalBusinessName = $data['legalBusinessName'] ?? null;
        $instance->businessWebsite = $data['businessWebsite'] ?? null;
        $instance->primaryBusinessAddress = $data['primaryBusinessAddress'] ?? null;
        $instance->city = $data['city'] ?? null;
        $instance->state = $data['state'] ?? null;
        $instance->phone = $data['phone'] ?? null;
        $instance->originatingServiceProvider = $data['originatingServiceProvider'] ?? null;
        $instance->zipCode = $data['zipCode'] ?? null;

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public static function validationRules(): array
    {
        return [
            'legalBusinessName' => ['required', 'string', 'max:255'],
            'businessWebsite' => ['nullable', 'url', 'max:255'],
            'primaryBusinessAddress' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'size:2'],
            'phone' => ['nullable', 'string', 'regex:/^\+?[\d\-\s()]+$/'],
            'zipCode' => ['required', 'string', 'regex:/^\d{5}(-\d{4})?$/'],
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
            $this->primaryBusinessAddress ?? '',
            $this->city ?? '',
            $this->state ?? '',
            $this->zipCode ?? ''
        );
    }
}
