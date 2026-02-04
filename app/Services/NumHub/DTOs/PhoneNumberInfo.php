<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Phone Number Info for display identity assignments.
 *
 * Contains caller name, logo, call reason, and associated phone numbers.
 */
final class PhoneNumberInfo extends BaseDTO
{
    /**
     * Associated phone numbers.
     *
     * @var array<int, string>|null
     */
    public ?array $phoneNumbers = null;

    /**
     * Caller name displayed on recipient's device (15-32 characters).
     */
    public ?string $callerName = null;

    /**
     * URL to logo image displayed on recipient's device.
     */
    public ?string $logoUrl = null;

    /**
     * Call reason displayed on recipient's device.
     */
    public ?string $callReason = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->phoneNumbers = $data['phoneNumbers'] ?? null;
        $instance->callerName = $data['callerName'] ?? null;
        $instance->logoUrl = $data['logoUrl'] ?? null;
        $instance->callReason = $data['callReason'] ?? null;

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public static function validationRules(): array
    {
        return [
            'callerName' => ['required', 'string', 'min:15', 'max:32'],
            'logoUrl' => ['nullable', 'url', 'max:2048'],
            'callReason' => ['nullable', 'string', 'max:255'],
            'phoneNumbers' => ['required', 'array', 'min:1'],
            'phoneNumbers.*' => ['string', 'regex:/^\+?1?\d{10}$/'],
        ];
    }

    /**
     * Add a phone number to this identity.
     *
     * @param  string  $phoneNumber  The phone number to add
     * @return self
     */
    public function addPhoneNumber(string $phoneNumber): self
    {
        if ($this->phoneNumbers === null) {
            $this->phoneNumbers = [];
        }

        if (! in_array($phoneNumber, $this->phoneNumbers, true)) {
            $this->phoneNumbers[] = $phoneNumber;
        }

        return $this;
    }

    /**
     * Normalize phone number to E.164 format.
     *
     * @param  string  $phoneNumber  Raw phone number
     * @return string
     */
    public static function normalizePhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digits
        $digits = preg_replace('/\D/', '', $phoneNumber);

        // Add +1 if 10 digits (US number)
        if (strlen($digits) === 10) {
            return '+1'.$digits;
        }

        // Add + if 11 digits starting with 1
        if (strlen($digits) === 11 && str_starts_with($digits, '1')) {
            return '+'.$digits;
        }

        return '+'.$digits;
    }
}
