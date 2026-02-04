<?php

declare(strict_types=1);

namespace App\Services\NumHub\Exceptions;

/**
 * Exception for authorization failures (403).
 */
class AuthorizationException extends NumHubException
{
    public function __construct(string $message = 'Access denied')
    {
        parent::__construct($message, 403);
    }
}
