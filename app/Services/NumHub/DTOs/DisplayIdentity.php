<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Display Identity for application updates.
 *
 * Used in UpdateBCApplicationModel for existing identities
 * that have a numhubIdentityId assigned.
 */
final class DisplayIdentity extends BaseDTO
{
    /**
     * List of display identities with phone number assignments.
     *
     * @var array<int, PhoneNumber>|null
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
                fn (array $identity) => PhoneNumber::fromArray($identity),
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
                fn (PhoneNumber $identity) => $identity->toArray(),
                $this->displayIdentities
            );
        }

        if ($this->loaUrl !== null) {
            $data['loaUrl'] = $this->loaUrl;
        }

        return $data;
    }
}
