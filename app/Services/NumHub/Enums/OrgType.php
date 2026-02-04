<?php

declare(strict_types=1);

namespace App\Services\NumHub\Enums;

/**
 * NumHub organization types.
 */
enum OrgType: string
{
    case NONE = 'None';
    case COMMERCIAL_ENTERPRISE = 'CommercialEnterprise';
    case GOVT_PUBLIC_SERVICE = 'GovtPublicService';
    case CHARITY_NON_PROFIT = 'CharityNonProfit';

    public function label(): string
    {
        return match ($this) {
            self::NONE => 'None',
            self::COMMERCIAL_ENTERPRISE => 'Commercial Enterprise',
            self::GOVT_PUBLIC_SERVICE => 'Government/Public Service',
            self::CHARITY_NON_PROFIT => 'Charity/Non-Profit',
        };
    }
}
