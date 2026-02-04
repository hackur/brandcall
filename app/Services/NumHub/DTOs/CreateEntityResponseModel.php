<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Create Entity Response Model DTO.
 *
 * Returned from POST/PUT /api/v1/application
 * Contains the newly created or updated entity IDs.
 */
final class CreateEntityResponseModel extends BaseDTO
{
    /**
     * NumHub entity UUID.
     */
    public ?string $numhubEntityId = null;

    /**
     * Application ID string.
     */
    public ?string $applicationId = null;

    /**
     * Enterprise ID.
     */
    public ?string $eid = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->numhubEntityId = $data['numhubEntityId'] ?? null;
        $instance->applicationId = $data['applicationId'] ?? null;
        $instance->eid = $data['eid'] ?? null;

        return $instance;
    }

    /**
     * Check if the response contains a valid entity ID.
     *
     * @return bool
     */
    public function hasEntityId(): bool
    {
        return $this->numhubEntityId !== null;
    }
}
