<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Phone Number DTO for existing display identities.
 *
 * Includes numhubIdentityId for updates to existing identities.
 */
final class PhoneNumber extends BaseDTO
{
    /**
     * NumHub identity UUID (set for existing identities).
     */
    public ?string $numhubIdentityId = null;

    /**
     * Associated phone numbers.
     *
     * @var array<int, string>|null
     */
    public ?array $phoneNumbers = null;

    /**
     * Caller name displayed on recipient's device.
     */
    public ?string $callerName = null;

    /**
     * URL to logo image.
     */
    public ?string $logoUrl = null;

    /**
     * Call reason text.
     */
    public ?string $callReason = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->numhubIdentityId = $data['numhubIdentityId'] ?? null;
        $instance->phoneNumbers = $data['phoneNumbers'] ?? null;
        $instance->callerName = $data['callerName'] ?? null;
        $instance->logoUrl = $data['logoUrl'] ?? null;
        $instance->callReason = $data['callReason'] ?? null;

        return $instance;
    }

    /**
     * Check if this is an existing identity (has UUID).
     *
     * @return bool
     */
    public function isExisting(): bool
    {
        return $this->numhubIdentityId !== null;
    }
}
