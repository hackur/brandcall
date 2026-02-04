<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

use DateTimeImmutable;

/**
 * User Information Response from NumHub.
 *
 * Contains details about the authenticated user.
 */
final class UserInfoResponse extends BaseDTO
{
    /**
     * Unique user identifier.
     */
    public ?int $userId = null;

    /**
     * Username for login.
     */
    public ?string $userName = null;

    /**
     * User's first name.
     */
    public ?string $firstName = null;

    /**
     * User's last name.
     */
    public ?string $lastName = null;

    /**
     * User's email address.
     */
    public ?string $email = null;

    /**
     * User's phone number.
     */
    public ?string $phoneNumber = null;

    /**
     * Whether SMS notifications are enabled.
     */
    public ?bool $sendTextMessage = null;

    /**
     * Whether MFA is required for this user.
     */
    public ?bool $requireMultiFactorAuth = null;

    /**
     * Whether the account is enabled.
     */
    public ?bool $enabled = null;

    /**
     * Last login timestamp.
     */
    public ?DateTimeImmutable $lastLoginDate = null;

    /**
     * Account creation timestamp.
     */
    public ?DateTimeImmutable $insertDate = null;

    /**
     * Name of user who created this account.
     */
    public ?string $insertName = null;

    /**
     * Last modification timestamp.
     */
    public ?DateTimeImmutable $modifiedDate = null;

    /**
     * Name of user who last modified this account.
     */
    public ?string $modifiedName = null;

    /**
     * Whether the password was auto-generated.
     */
    public bool $isPasswordGenerated = false;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->userId = isset($data['userId']) ? (int) $data['userId'] : null;
        $instance->userName = $data['userName'] ?? null;
        $instance->firstName = $data['firstName'] ?? null;
        $instance->lastName = $data['lastName'] ?? null;
        $instance->email = $data['email'] ?? null;
        $instance->phoneNumber = $data['phoneNumber'] ?? null;
        $instance->sendTextMessage = isset($data['sendTextMessage']) ? (bool) $data['sendTextMessage'] : null;
        $instance->requireMultiFactorAuth = isset($data['requireMultiFactorAuth']) ? (bool) $data['requireMultiFactorAuth'] : null;
        $instance->enabled = isset($data['enabled']) ? (bool) $data['enabled'] : null;
        $instance->isPasswordGenerated = (bool) ($data['isPasswordGenerated'] ?? false);

        // Parse dates
        if (! empty($data['lastLoginDate'])) {
            $instance->lastLoginDate = new DateTimeImmutable($data['lastLoginDate']);
        }
        if (! empty($data['insertDate'])) {
            $instance->insertDate = new DateTimeImmutable($data['insertDate']);
        }
        if (! empty($data['modifiedDate'])) {
            $instance->modifiedDate = new DateTimeImmutable($data['modifiedDate']);
        }

        $instance->insertName = $data['insertName'] ?? null;
        $instance->modifiedName = $data['modifiedName'] ?? null;

        return $instance;
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return trim(sprintf('%s %s', $this->firstName ?? '', $this->lastName ?? ''));
    }

    /**
     * Check if the user account is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->enabled === true;
    }
}
