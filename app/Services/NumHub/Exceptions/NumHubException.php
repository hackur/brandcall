<?php

declare(strict_types=1);

namespace App\Services\NumHub\Exceptions;

use Exception;

/**
 * Base exception for NumHub API errors.
 */
class NumHubException extends Exception
{
    public function __construct(string $message = 'NumHub API error', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
