<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * User Client DTO for NumHub client associations.
 *
 * Represents a client account accessible by a user.
 */
final class UserClientDTO extends BaseDTO
{
    /**
     * User ID this client is associated with.
     */
    public ?int $userId = null;

    /**
     * Client ID for API requests.
     */
    public ?int $clientId = null;

    /**
     * Client company name.
     */
    public ?string $clientName = null;

    /**
     * Responsible Organization ID.
     */
    public ?string $respOrgId = null;

    /**
     * Whether this client is assigned to the user.
     */
    public ?bool $assigned = null;

    /**
     * Whether this client has BCID enabled.
     */
    public ?bool $hasBcid = null;

    /**
     * Whether this is an OSP (Originating Service Provider) account.
     */
    public ?bool $isOsp = null;

    /**
     * OSP identifier if this is an OSP account.
     */
    public ?string $ospId = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->userId = isset($data['userId']) ? (int) $data['userId'] : null;
        $instance->clientId = isset($data['clientId']) ? (int) $data['clientId'] : null;
        $instance->clientName = $data['clientName'] ?? null;
        $instance->respOrgId = $data['respOrgId'] ?? null;
        $instance->assigned = isset($data['assigned']) ? (bool) $data['assigned'] : null;
        $instance->hasBcid = isset($data['hasBcid']) ? (bool) $data['hasBcid'] : null;
        $instance->isOsp = isset($data['isOsp']) ? (bool) $data['isOsp'] : null;
        $instance->ospId = $data['ospId'] ?? null;

        return $instance;
    }

    /**
     * Check if this client can use BCID features.
     *
     * @return bool
     */
    public function canUseBcid(): bool
    {
        return $this->hasBcid === true && $this->assigned === true;
    }
}
