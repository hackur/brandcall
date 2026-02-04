<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

use DateTimeImmutable;

/**
 * Settlement Report Response DTO.
 *
 * Returned from GET /api/v1/confirmationReports/settlementReports
 * Contains BCID usage and billing settlement data.
 */
final class SettlementReportResponse extends BaseDTO
{
    /**
     * Unique log ID.
     */
    public ?string $id = null;

    /**
     * Insertion timestamp.
     */
    public ?DateTimeImmutable $insertedTimestamp = null;

    /**
     * Enterprise ID.
     */
    public ?string $eId = null;

    /**
     * BPO ID.
     */
    public ?string $bpoId = null;

    /**
     * Destination phone number.
     */
    public ?string $dest = null;

    /**
     * VA ID.
     */
    public ?string $vaid = null;

    /**
     * Claims passed.
     */
    public ?string $claimsPassed = null;

    /**
     * TSP ID.
     */
    public ?string $tspId = null;

    /**
     * TSP fee amount.
     */
    public float $tspFee = 0.0;

    /**
     * Originating phone number.
     */
    public ?string $orig = null;

    /**
     * OSP ID.
     */
    public ?string $ospId = null;

    /**
     * TSP name.
     */
    public ?string $tspName = null;

    /**
     * Directory ID.
     */
    public ?string $dirId = null;

    /**
     * Claims requested.
     */
    public ?string $claimsRequested = null;

    /**
     * SA ID.
     */
    public ?string $said = null;

    /**
     * IAT timestamp (Unix epoch).
     */
    public int $iat = 0;

    /**
     * OA ID.
     */
    public ?string $oaid = null;

    /**
     * IAT as datetime.
     */
    public ?DateTimeImmutable $iatTimeStamp = null;

    /**
     * BCID internal ID.
     */
    public ?string $bcidInternalId = null;

    /**
     * Signature.
     */
    public ?string $sig = null;

    /**
     * Call reference number.
     */
    public ?string $crn = null;

    /**
     * Additional metadata.
     */
    public ?string $metadata = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->id = $data['id'] ?? null;
        $instance->eId = $data['eId'] ?? null;
        $instance->bpoId = $data['bpoId'] ?? null;
        $instance->dest = $data['dest'] ?? null;
        $instance->vaid = $data['vaid'] ?? null;
        $instance->claimsPassed = $data['claims_Passed'] ?? $data['claimsPassed'] ?? null;
        $instance->tspId = $data['tspId'] ?? null;
        $instance->tspFee = (float) ($data['tsp_Fee'] ?? $data['tspFee'] ?? 0.0);
        $instance->orig = $data['orig'] ?? null;
        $instance->ospId = $data['ospId'] ?? null;
        $instance->tspName = $data['tsp_Name'] ?? $data['tspName'] ?? null;
        $instance->dirId = $data['dirId'] ?? null;
        $instance->claimsRequested = $data['claims_Requested'] ?? $data['claimsRequested'] ?? null;
        $instance->said = $data['said'] ?? null;
        $instance->iat = (int) ($data['iat'] ?? 0);
        $instance->oaid = $data['oaid'] ?? null;
        $instance->bcidInternalId = $data['bcidInternalId'] ?? null;
        $instance->sig = $data['sig'] ?? null;
        $instance->crn = $data['crn'] ?? null;
        $instance->metadata = $data['metadata'] ?? null;

        if (! empty($data['inserted_Timestamp'] ?? $data['insertedTimestamp'])) {
            $instance->insertedTimestamp = new DateTimeImmutable($data['inserted_Timestamp'] ?? $data['insertedTimestamp']);
        }
        if (! empty($data['iatTimeStamp'])) {
            $instance->iatTimeStamp = new DateTimeImmutable($data['iatTimeStamp']);
        }

        return $instance;
    }

    /**
     * Create collection from API response.
     *
     * @param  array<string, mixed>  $response  API response with result array
     * @return array<int, static>
     */
    public static function fromApiResponse(array $response): array
    {
        $result = $response['result'] ?? [];

        if (! is_array($result)) {
            return [];
        }

        return array_map(fn (array $item) => static::fromArray($item), $result);
    }

    /**
     * Check if claims were successful.
     *
     * @return bool
     */
    public function wasSuccessful(): bool
    {
        return $this->claimsPassed !== null && $this->claimsPassed !== '';
    }
}
