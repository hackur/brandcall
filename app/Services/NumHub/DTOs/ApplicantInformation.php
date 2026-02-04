<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Applicant Information section of a BCID application.
 *
 * Contains contact details for the person submitting the application.
 */
final class ApplicantInformation extends BaseDTO
{
    /**
     * Applicant's first name.
     */
    public ?string $firstName = null;

    /**
     * Applicant's last name.
     */
    public ?string $lastName = null;

    /**
     * Applicant's email address.
     */
    public ?string $emailAddress = null;

    /**
     * Applicant's phone number.
     */
    public ?string $phoneNumber = null;

    /**
     * Associated OSP IDs.
     *
     * @var array<int, string>|null
     */
    public ?array $ospIds = null;

    /**
     * Whether email has been verified via OTP.
     */
    public bool $isVerified = false;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->firstName = $data['firstName'] ?? null;
        $instance->lastName = $data['lastName'] ?? null;
        $instance->emailAddress = $data['emailAddress'] ?? null;
        $instance->phoneNumber = $data['phoneNumber'] ?? null;
        $instance->ospIds = $data['ospIds'] ?? null;
        $instance->isVerified = (bool) ($data['isVerified'] ?? false);

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public static function validationRules(): array
    {
        return [
            'firstName' => ['required', 'string', 'max:50'],
            'lastName' => ['required', 'string', 'max:50'],
            'emailAddress' => ['required', 'email', 'max:255'],
            'phoneNumber' => ['required', 'string', 'regex:/^\+?[\d\-\s()]+$/'],
        ];
    }

    /**
     * Get the applicant's full name.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return trim(sprintf('%s %s', $this->firstName ?? '', $this->lastName ?? ''));
    }
}
