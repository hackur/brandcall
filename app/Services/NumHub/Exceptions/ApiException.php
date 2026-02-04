<?php

declare(strict_types=1);

namespace App\Services\NumHub\Exceptions;

/**
 * Exception for general API errors (5xx).
 */
class ApiException extends NumHubException
{
    public function __construct(string $message = 'API error', int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
