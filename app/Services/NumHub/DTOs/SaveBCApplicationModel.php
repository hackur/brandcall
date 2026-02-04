<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

/**
 * Save BC Application Model - Create Request.
 *
 * Used for POST /api/v1/application to create a new BCID application.
 *
 * Status values:
 * - "Saved" (1): Save as draft
 * - "Submit" (2): Submit for processing
 */
final class SaveBCApplicationModel extends BaseDTO
{
    public const STATUS_SAVED = 'Saved';

    public const STATUS_SUBMIT = 'Submit';

    /**
     * Applicant contact information.
     */
    public ?ApplicantInformation $applicantInformation = null;

    /**
     * Business entity details.
     */
    public ?BusinessEntityDetails $businessEntityDetails = null;

    /**
     * Business identity/registration numbers.
     */
    public ?BusinessIdentityDetails $businessIdentityDetails = null;

    /**
     * Consumer protection contact information.
     */
    public ?ConsumerProtectionContacts $consumerProtectionContacts = null;

    /**
     * Business agent of record information.
     */
    public ?BusinessAgentOfRecord $businessAgentOfRecord = null;

    /**
     * Business references.
     */
    public ?References $references = null;

    /**
     * Display identity information (caller IDs).
     */
    public ?DisplayIdentityInfo $displayIdentityInformation = null;

    /**
     * Certification/attestation.
     */
    public ?RegisterCertificate $registerCertificate = null;

    /**
     * Current section number (1-8).
     */
    public int $section = 1;

    /**
     * Application status: "Saved" or "Submit".
     */
    public string $status = self::STATUS_SAVED;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        if (isset($data['applicantInformation']) && is_array($data['applicantInformation'])) {
            $instance->applicantInformation = ApplicantInformation::fromArray($data['applicantInformation']);
        }

        if (isset($data['businessEntityDetails']) && is_array($data['businessEntityDetails'])) {
            $instance->businessEntityDetails = BusinessEntityDetails::fromArray($data['businessEntityDetails']);
        }

        if (isset($data['businessIdentityDetails']) && is_array($data['businessIdentityDetails'])) {
            $instance->businessIdentityDetails = BusinessIdentityDetails::fromArray($data['businessIdentityDetails']);
        }

        if (isset($data['consumerProtectionContacts']) && is_array($data['consumerProtectionContacts'])) {
            $instance->consumerProtectionContacts = ConsumerProtectionContacts::fromArray($data['consumerProtectionContacts']);
        }

        if (isset($data['businessAgentOfRecord']) && is_array($data['businessAgentOfRecord'])) {
            $instance->businessAgentOfRecord = BusinessAgentOfRecord::fromArray($data['businessAgentOfRecord']);
        }

        if (isset($data['references']) && is_array($data['references'])) {
            $instance->references = References::fromArray($data['references']);
        }

        if (isset($data['displayIdentityInformation']) && is_array($data['displayIdentityInformation'])) {
            $instance->displayIdentityInformation = DisplayIdentityInfo::fromArray($data['displayIdentityInformation']);
        }

        if (isset($data['registerCertificate']) && is_array($data['registerCertificate'])) {
            $instance->registerCertificate = RegisterCertificate::fromArray($data['registerCertificate']);
        }

        $instance->section = (int) ($data['section'] ?? 1);
        $instance->status = $data['status'] ?? self::STATUS_SAVED;

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_filter([
            'applicantInformation' => $this->applicantInformation?->toArray(),
            'businessEntityDetails' => $this->businessEntityDetails?->toArray(),
            'businessIdentityDetails' => $this->businessIdentityDetails?->toArray(),
            'consumerProtectionContacts' => $this->consumerProtectionContacts?->toArray(),
            'businessAgentOfRecord' => $this->businessAgentOfRecord?->toArray(),
            'references' => $this->references?->toArray(),
            'displayIdentityInformation' => $this->displayIdentityInformation?->toArray(),
            'registerCertificate' => $this->registerCertificate?->toArray(),
            'section' => $this->section,
            'status' => $this->status,
        ], fn ($value) => $value !== null);
    }

    /**
     * Mark as submitted for processing.
     *
     * @return self
     */
    public function submit(): self
    {
        $this->status = self::STATUS_SUBMIT;

        return $this;
    }

    /**
     * Check if all required sections are complete.
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->applicantInformation !== null
            && $this->businessEntityDetails !== null
            && $this->businessIdentityDetails !== null
            && $this->consumerProtectionContacts !== null
            && $this->displayIdentityInformation !== null
            && $this->registerCertificate?->isComplete() === true;
    }
}
