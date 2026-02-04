<?php

declare(strict_types=1);

namespace App\Services\NumHub\Exceptions;

/**
 * Exception for entity not found (404).
 */
class EntityNotFoundException extends NumHubException
{
    public function __construct(string $message = 'Entity not found')
    {
        parent::__construct($message, 404);
    }
}
