<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Business Identity Details section of a BCID application.
 *
 * Contains business identification numbers and registration information.
 */
final class BusinessIdentityDetails extends BaseDTO
{
    /**
     * Federal Employer Identification Number (EIN/FEIN).
     */
    public ?string $federalEmployerIdNumber = null;

    /**
     * Dun & Bradstreet D-U-N-S Number.
     */
    public ?string $dunAndBradstreetNumber = null;

    /**
     * State corporate registration number.
     */
    public ?string $stateCorporateRegistrationNumber = null;

    /**
     * State professional license number.
     */
    public ?string $stateProfessionalLicenseNumber = null;

    /**
     * Primary Business Domain SIC Code.
     */
    public ?string $primaryBusinessDomainSicCode = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->federalEmployerIdNumber = $data['federalEmployerIdNumber'] ?? null;
        $instance->dunAndBradstreetNumber = $data['dunAndBradstreetNumber'] ?? null;
        $instance->stateCorporateRegistrationNumber = $data['stateCorporateRegistrationNumber'] ?? null;
        $instance->stateProfessionalLicenseNumber = $data['stateProfessionalLicenseNumber'] ?? null;
        $instance->primaryBusinessDomainSicCode = $data['primaryBusinessDomainSicCode'] ?? null;

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public static function validationRules(): array
    {
        return [
            'federalEmployerIdNumber' => ['nullable', 'string', 'regex:/^\d{2}-\d{7}$/'],
            'dunAndBradstreetNumber' => ['nullable', 'string', 'regex:/^\d{9}$/'],
            'stateCorporateRegistrationNumber' => ['nullable', 'string', 'max:50'],
            'stateProfessionalLicenseNumber' => ['nullable', 'string', 'max:50'],
            'primaryBusinessDomainSicCode' => ['nullable', 'string', 'regex:/^\d{4}$/'],
        ];
    }

    /**
     * Check if at least one identifier is provided.
     *
     * @return bool
     */
    public function hasIdentifier(): bool
    {
        return $this->federalEmployerIdNumber !== null
            || $this->dunAndBradstreetNumber !== null
            || $this->stateCorporateRegistrationNumber !== null
            || $this->stateProfessionalLicenseNumber !== null;
    }
}
