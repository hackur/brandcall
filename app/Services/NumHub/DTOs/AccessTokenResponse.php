<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Access Token Response from NumHub authentication.
 *
 * Returned from POST /api/v1/authorize/token
 * Contains the Bearer token and user information for API access.
 */
final class AccessTokenResponse extends BaseDTO
{
    /**
     * JWT access token for API authentication.
     */
    public ?string $accessToken = null;

    /**
     * Token type (always "Bearer").
     */
    public ?string $tokenType = null;

    /**
     * Seconds until token expires (86400 = 24 hours).
     */
    public int $expiresIn = 0;

    /**
     * User details associated with this token.
     */
    public ?UserInfoResponse $user = null;

    /**
     * User's assigned roles.
     *
     * @var array<int, UserRoleDTO>|null
     */
    public ?array $userRoles = null;

    /**
     * Clients accessible by this user.
     *
     * @var array<int, UserClientDTO>|null
     */
    public ?array $userClients = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->accessToken = $data['accessToken'] ?? null;
        $instance->tokenType = $data['tokenType'] ?? null;
        $instance->expiresIn = (int) ($data['expiresIn'] ?? 0);

        if (isset($data['user']) && is_array($data['user'])) {
            $instance->user = UserInfoResponse::fromArray($data['user']);
        }

        if (isset($data['userRoles']) && is_array($data['userRoles'])) {
            $instance->userRoles = array_map(
                fn (array $role) => UserRoleDTO::fromArray($role),
                $data['userRoles']
            );
        }

        if (isset($data['userClients']) && is_array($data['userClients'])) {
            $instance->userClients = array_map(
                fn (array $client) => UserClientDTO::fromArray($client),
                $data['userClients']
            );
        }

        return $instance;
    }

    /**
     * Check if the token is expired.
     *
     * @param  int  $bufferSeconds  Buffer time before actual expiry
     * @return bool
     */
    public function isExpired(int $bufferSeconds = 300): bool
    {
        return $this->expiresIn <= $bufferSeconds;
    }

    /**
     * Get the Authorization header value.
     *
     * @return string
     */
    public function getAuthorizationHeader(): string
    {
        return sprintf('%s %s', $this->tokenType ?? 'Bearer', $this->accessToken ?? '');
    }

    /**
     * Get the first client ID available to this user.
     *
     * @return int|null
     */
    public function getFirstClientId(): ?int
    {
        return $this->userClients[0]?->clientId ?? null;
    }

    /**
     * Check if user has a specific role.
     *
     * @param  string  $roleType  The role type to check
     * @return bool
     */
    public function hasRole(string $roleType): bool
    {
        if ($this->userRoles === null) {
            return false;
        }

        foreach ($this->userRoles as $role) {
            if ($role->roleType === $roleType && $role->assigned) {
                return true;
            }
        }

        return false;
    }
}
