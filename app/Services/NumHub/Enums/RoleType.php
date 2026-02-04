<?php

declare(strict_types=1);

namespace App\Services\NumHub\Enums;

/**
 * NumHub deal role types.
 */
enum RoleType: string
{
    case NONE = 'None';
    case ENTERPRISE = 'Enterprise';
    case BPO = 'BPO';

    public function label(): string
    {
        return match ($this) {
            self::NONE => 'None',
            self::ENTERPRISE => 'Enterprise',
            self::BPO => 'Business Process Outsourcer',
        };
    }
}
