<?php

declare(strict_types=1);

namespace App\Services\NumHub\Exceptions;

/**
 * Exception for rate limit exceeded (429).
 */
class RateLimitException extends NumHubException
{
    public function __construct(
        string $message = 'Rate limit exceeded',
        public readonly int $retryAfter = 60
    ) {
        parent::__construct($message, 429);
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}
