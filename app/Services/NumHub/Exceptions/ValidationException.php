<?php

declare(strict_types=1);

namespace App\Services\NumHub\Exceptions;

/**
 * Exception for validation errors (400).
 */
class ValidationException extends NumHubException
{
    /**
     * @param array<string, array<string>> $errors
     */
    public function __construct(
        string $message = 'Validation failed',
        public readonly array $errors = []
    ) {
        parent::__construct($message, 400);
    }

    /**
     * @return array<string, array<string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
