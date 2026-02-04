<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Display Identity Information for application submission.
 *
 * Contains caller ID display identities with phone numbers,
 * used when creating/updating BCID applications.
 */
final class DisplayIdentityInfo extends BaseDTO
{
    /**
     * List of display identities with phone number assignments.
     *
     * @var array<int, PhoneNumberInfo>|null
     */
    public ?array $displayIdentities = null;

    /**
     * URL to the Letter of Authorization document.
     */
    public ?string $loaUrl = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        if (isset($data['displayIdentities']) && is_array($data['displayIdentities'])) {
            $instance->displayIdentities = array_map(
                fn (array $identity) => PhoneNumberInfo::fromArray($identity),
                $data['displayIdentities']
            );
        }

        $instance->loaUrl = $data['loaUrl'] ?? null;

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->displayIdentities !== null) {
            $data['displayIdentities'] = array_map(
                fn (PhoneNumberInfo $identity) => $identity->toArray(),
                $this->displayIdentities
            );
        }

        if ($this->loaUrl !== null) {
            $data['loaUrl'] = $this->loaUrl;
        }

        return $data;
    }

    /**
     * Get total phone number count across all identities.
     *
     * @return int
     */
    public function getTotalPhoneCount(): int
    {
        if ($this->displayIdentities === null) {
            return 0;
        }

        return array_reduce(
            $this->displayIdentities,
            fn (int $carry, PhoneNumberInfo $identity) => $carry + count($identity->phoneNumbers ?? []),
            0
        );
    }
}
