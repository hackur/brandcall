<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;

/**
 * Base Data Transfer Object for NumHub API integration.
 *
 * Provides common functionality for converting between API responses
 * and Laravel models, including property mapping and validation.
 *
 * @template T of BaseDTO
 */
abstract class BaseDTO implements Arrayable, JsonSerializable
{
    /**
     * Create a new DTO instance from an array of data.
     *
     * Maps API property names (camelCase) to DTO properties.
     *
     * @param  array<string, mixed>  $data  Raw API response data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;
        $reflection = new ReflectionClass($instance);

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            $snakeName = Str::snake($propertyName);

            // Try camelCase first (API format), then snake_case
            $value = $data[$propertyName] ?? $data[$snakeName] ?? null;

            if ($value !== null) {
                $instance->{$propertyName} = static::castValue($property, $value);
            }
        }

        return $instance;
    }

    /**
     * Create multiple DTO instances from an array of data.
     *
     * @param  array<int, array<string, mixed>>  $items  Array of raw API data
     * @return array<int, static>
     */
    public static function fromArrayCollection(array $items): array
    {
        return array_map(fn (array $item) => static::fromArray($item), $items);
    }

    /**
     * Convert the DTO to an array.
     *
     * Converts property names to camelCase for API requests.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $data = [];

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            $value = $this->{$propertyName} ?? null;

            if ($value === null) {
                continue;
            }

            // Convert nested DTOs
            if ($value instanceof BaseDTO) {
                $value = $value->toArray();
            } elseif (is_array($value)) {
                $value = array_map(
                    fn ($item) => $item instanceof BaseDTO ? $item->toArray() : $item,
                    $value
                );
            }

            $data[$propertyName] = $value;
        }

        return $data;
    }

    /**
     * Convert the DTO to an array for API requests.
     *
     * Returns data in the format expected by NumHub API.
     *
     * @return array<string, mixed>
     */
    public function toApiArray(): array
    {
        return $this->toArray();
    }

    /**
     * Convert to JSON-serializable format.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get validation rules for this DTO.
     *
     * Override in child classes to provide specific validation rules.
     *
     * @return array<string, string|array<int, mixed>>
     */
    public static function validationRules(): array
    {
        return [];
    }

    /**
     * Validate the DTO data against its rules.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validate(): \Illuminate\Contracts\Validation\Validator
    {
        return validator($this->toArray(), static::validationRules());
    }

    /**
     * Check if the DTO data is valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->validate()->passes();
    }

    /**
     * Cast a value to the appropriate type based on property reflection.
     *
     * @param  ReflectionProperty  $property  The property reflection
     * @param  mixed  $value  The value to cast
     * @return mixed
     */
    protected static function castValue(ReflectionProperty $property, mixed $value): mixed
    {
        $type = $property->getType();

        if ($type === null) {
            return $value;
        }

        $typeName = $type->getName();

        // Handle nullable types
        if ($value === null && $type->allowsNull()) {
            return null;
        }

        // Handle built-in types
        return match ($typeName) {
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => (bool) $value,
            'string' => (string) $value,
            'array' => (array) $value,
            default => static::castObject($typeName, $value),
        };
    }

    /**
     * Cast a value to an object type.
     *
     * @param  string  $typeName  The type name
     * @param  mixed  $value  The value to cast
     * @return mixed
     */
    protected static function castObject(string $typeName, mixed $value): mixed
    {
        // Handle DateTimeInterface
        if (is_a($typeName, \DateTimeInterface::class, true)) {
            if ($value instanceof \DateTimeInterface) {
                return $value;
            }

            return new \DateTimeImmutable($value);
        }

        // Handle nested DTOs
        if (is_a($typeName, self::class, true) && is_array($value)) {
            return $typeName::fromArray($value);
        }

        return $value;
    }
}
