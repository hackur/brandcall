<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * References section of a BCID application.
 *
 * Contains business references and financial institution details.
 */
final class References extends BaseDTO
{
    // Reference 1
    public ?string $referenceOneName = null;

    public ?string $referenceOneTitle = null;

    public ?string $referenceOnePhone = null;

    public ?string $referenceOneEmail = null;

    // Reference 2
    public ?string $referenceTwoName = null;

    public ?string $referenceTwoTitle = null;

    public ?string $referenceTwoPhone = null;

    public ?string $referenceTwoEmail = null;

    // Financial Institution
    public ?string $financialInstitutionName = null;

    public ?string $streetAddress = null;

    public ?string $city = null;

    public ?string $state = null;

    public ?string $zipCode = null;

    public ?string $contactName = null;

    public ?string $phoneNumber = null;

    public ?string $emailAddress = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        // Reference 1
        $instance->referenceOneName = $data['referenceOneName'] ?? null;
        $instance->referenceOneTitle = $data['referenceOneTitle'] ?? null;
        $instance->referenceOnePhone = $data['referenceOnePhone'] ?? null;
        $instance->referenceOneEmail = $data['referenceOneEmail'] ?? null;

        // Reference 2
        $instance->referenceTwoName = $data['referenceTwoName'] ?? null;
        $instance->referenceTwoTitle = $data['referenceTwoTitle'] ?? null;
        $instance->referenceTwoPhone = $data['referenceTwoPhone'] ?? null;
        $instance->referenceTwoEmail = $data['referenceTwoEmail'] ?? null;

        // Financial Institution
        $instance->financialInstitutionName = $data['financialInstitutionName'] ?? null;
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
            'referenceOneName' => ['nullable', 'string', 'max:100'],
            'referenceOneTitle' => ['nullable', 'string', 'max:100'],
            'referenceOnePhone' => ['nullable', 'string', 'regex:/^\+?[\d\-\s()]+$/'],
            'referenceOneEmail' => ['nullable', 'email', 'max:255'],
            'referenceTwoName' => ['nullable', 'string', 'max:100'],
            'referenceTwoTitle' => ['nullable', 'string', 'max:100'],
            'referenceTwoPhone' => ['nullable', 'string', 'regex:/^\+?[\d\-\s()]+$/'],
            'referenceTwoEmail' => ['nullable', 'email', 'max:255'],
            'financialInstitutionName' => ['nullable', 'string', 'max:255'],
            'streetAddress' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'size:2'],
            'zipCode' => ['nullable', 'string', 'regex:/^\d{5}(-\d{4})?$/'],
            'contactName' => ['nullable', 'string', 'max:100'],
            'phoneNumber' => ['nullable', 'string', 'regex:/^\+?[\d\-\s()]+$/'],
            'emailAddress' => ['nullable', 'email', 'max:255'],
        ];
    }
}
