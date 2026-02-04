<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Phone Number Upload Request DTO.
 *
 * Used for POST /api/v1/applications/{NumhubIdentityId}/uploadadditionaltns
 * Bulk uploads phone numbers to an existing display identity.
 */
final class PhoneNumberUploadRequest extends BaseDTO
{
    /**
     * NumHub identity UUID to add phone numbers to.
     */
    public ?string $numhubIdentityId = null;

    /**
     * Phone numbers to upload.
     *
     * @var array<int, string>|null
     */
    public ?array $phoneNumbers = null;

    /**
     * Path to uploaded file containing phone numbers.
     */
    public ?string $filePath = null;

    /**
     * Fee acknowledgement.
     */
    public bool $isFeeChecked = false;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->numhubIdentityId = $data['numhubIdentityId'] ?? null;
        $instance->phoneNumbers = $data['phoneNumbers'] ?? null;
        $instance->filePath = $data['filePath'] ?? null;
        $instance->isFeeChecked = (bool) ($data['isFeeChecked'] ?? false);

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public static function validationRules(): array
    {
        return [
            'numhubIdentityId' => ['required', 'uuid'],
            'phoneNumbers' => ['required_without:filePath', 'array', 'min:1'],
            'phoneNumbers.*' => ['string', 'regex:/^\+?1?\d{10}$/'],
            'filePath' => ['required_without:phoneNumbers', 'string'],
            'isFeeChecked' => ['required', 'boolean', 'accepted'],
        ];
    }

    /**
     * Create from an array of phone numbers.
     *
     * @param  string  $identityId  NumHub identity UUID
     * @param  array<int, string>  $phoneNumbers  Phone numbers
     * @return static
     */
    public static function fromPhoneNumbers(string $identityId, array $phoneNumbers): static
    {
        $instance = new static;
        $instance->numhubIdentityId = $identityId;
        $instance->phoneNumbers = array_map(
            fn (string $number) => PhoneNumberInfo::normalizePhoneNumber($number),
            $phoneNumbers
        );
        $instance->isFeeChecked = true;

        return $instance;
    }

    /**
     * Get phone number count.
     *
     * @return int
     */
    public function getPhoneCount(): int
    {
        return count($this->phoneNumbers ?? []);
    }
}
