<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Get New Display Identity - Response format.
 *
 * Contains display identities with full metadata as returned from API.
 */
final class GetNewDisplayIdentity extends BaseDTO
{
    /**
     * List of display identities with full info.
     *
     * @var array<int, GetNewDisplayIdentityInfo>|null
     */
    public ?array $displayIdentities = null;

    /**
     * URL to the Letter of Authorization document.
     */
    public ?string $loaUrl = null;

    /**
     * LOA document UUID.
     */
    public ?string $loaId = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        if (isset($data['displayIdentities']) && is_array($data['displayIdentities'])) {
            $instance->displayIdentities = array_map(
                fn (array $identity) => GetNewDisplayIdentityInfo::fromArray($identity),
                $data['displayIdentities']
            );
        }

        $instance->loaUrl = $data['loaUrl'] ?? null;
        $instance->loaId = $data['loaId'] ?? null;

        return $instance;
    }

    /**
     * Get total phone number count.
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
            fn (int $carry, GetNewDisplayIdentityInfo $identity) => $carry + count($identity->phoneNumbers ?? []),
            0
        );
    }

    /**
     * Get identity count.
     *
     * @return int
     */
    public function getIdentityCount(): int
    {
        return count($this->displayIdentities ?? []);
    }
}
