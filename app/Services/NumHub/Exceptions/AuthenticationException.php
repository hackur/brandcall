<?php

declare(strict_types=1);

namespace App\Services\NumHub\Exceptions;

/**
 * Exception for authentication failures (401).
 */
class AuthenticationException extends NumHubException
{
    public function __construct(string $message = 'Authentication failed')
    {
        parent::__construct($message, 401);
    }
}
