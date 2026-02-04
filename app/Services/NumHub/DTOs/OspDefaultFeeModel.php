<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * OSP Default Fee Model DTO.
 *
 * Used for POST/PUT /api/v1/deals/fees/{ospId}
 * Sets default fee structure for an OSP.
 */
final class OspDefaultFeeModel extends BaseDTO
{
    /**
     * Vetting fee amount (per application).
     */
    public float $vettingFee = 0.0;

    /**
     * Retail fee per confirmed delivery.
     */
    public float $retailFeePerConfirmedDelivery = 0.0;

    /**
     * Platform fee per identity (monthly).
     */
    public float $platformFeePerIdentity = 0.0;

    /**
     * Whether to apply these fees to new deal registrations.
     */
    public bool $applyToDealRegistration = false;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->vettingFee = (float) ($data['vettingFee'] ?? 0.0);
        $instance->retailFeePerConfirmedDelivery = (float) ($data['retailFeePerConfirmedDelivery'] ?? 0.0);
        $instance->platformFeePerIdentity = (float) ($data['platformFeePerIdentity'] ?? 0.0);
        $instance->applyToDealRegistration = (bool) ($data['applyToDealRegistration'] ?? false);

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public static function validationRules(): array
    {
        return [
            'vettingFee' => ['required', 'numeric', 'min:0'],
            'retailFeePerConfirmedDelivery' => ['required', 'numeric', 'min:0'],
            'platformFeePerIdentity' => ['required', 'numeric', 'min:0'],
            'applyToDealRegistration' => ['required', 'boolean'],
        ];
    }

    /**
     * Calculate estimated monthly cost.
     *
     * @param  int  $identityCount  Number of identities
     * @param  int  $estimatedDeliveries  Estimated monthly deliveries
     * @return float
     */
    public function calculateMonthlyCost(int $identityCount, int $estimatedDeliveries): float
    {
        return ($this->platformFeePerIdentity * $identityCount)
            + ($this->retailFeePerConfirmedDelivery * $estimatedDeliveries);
    }
}
