<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * User Role DTO for NumHub user permissions.
 *
 * Represents a role assignment for a user in the NumHub system.
 */
final class UserRoleDTO extends BaseDTO
{
    /**
     * User ID this role is assigned to.
     */
    public ?int $userId = null;

    /**
     * Role type identifier.
     *
     * Common values:
     * - GlobalAdmin
     * - OSPAdmin
     * - Enterprise
     * - EditUsers
     * - ViewTemplates
     */
    public ?string $roleType = null;

    /**
     * Human-readable role description.
     */
    public ?string $description = null;

    /**
     * Whether this role is assigned to the user.
     */
    public ?bool $assigned = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->userId = isset($data['userId']) ? (int) $data['userId'] : null;
        $instance->roleType = $data['roleType'] ?? null;
        $instance->description = $data['description'] ?? null;
        $instance->assigned = isset($data['assigned']) ? (bool) $data['assigned'] : null;

        return $instance;
    }

    /**
     * Check if this is an admin role.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array($this->roleType, ['GlobalAdmin', 'OSPAdmin'], true);
    }
}
