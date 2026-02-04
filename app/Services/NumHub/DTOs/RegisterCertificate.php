<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

use DateTimeImmutable;

/**
 * Register Certificate section of a BCID application.
 *
 * Contains the certification/attestation that the applicant
 * agrees to terms and confirms information accuracy.
 */
final class RegisterCertificate extends BaseDTO
{
    /**
     * Full name of the person certifying.
     */
    public ?string $fullName = null;

    /**
     * Date of certification.
     */
    public ?DateTimeImmutable $date = null;

    /**
     * Whether the applicant has certified the information.
     */
    public bool $certified = false;

    /**
     * Whether the fee terms have been acknowledged.
     */
    public bool $isFeeChecked = false;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->fullName = $data['fullName'] ?? null;
        $instance->certified = (bool) ($data['certified'] ?? false);
        $instance->isFeeChecked = (bool) ($data['isFeeChecked'] ?? false);

        if (! empty($data['date'])) {
            $instance->date = new DateTimeImmutable($data['date']);
        }

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'fullName' => $this->fullName,
            'date' => $this->date?->format('c'),
            'certified' => $this->certified,
            'isFeeChecked' => $this->isFeeChecked,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function validationRules(): array
    {
        return [
            'fullName' => ['required', 'string', 'max:255'],
            'certified' => ['required', 'boolean', 'accepted'],
            'isFeeChecked' => ['required', 'boolean', 'accepted'],
        ];
    }

    /**
     * Check if the certification is complete.
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->fullName !== null
            && $this->certified === true
            && $this->isFeeChecked === true;
    }
}
