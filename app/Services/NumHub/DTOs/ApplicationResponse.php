<?php

declare(strict_types=1);

namespace App\Services\NumHub\DTOs;

use DateTimeImmutable;

/**
 * Application Response DTO.
 *
 * Returned from GET /api/v1/application/{NumhubEntityId}
 * Contains full application details including status and identities.
 */
final class ApplicationResponse extends BaseDTO
{
    /**
     * NumHub entity UUID.
     */
    public ?string $numhubEntityId = null;

    /**
     * Application ID string.
     */
    public ?string $applicationId = null;

    /**
     * Current section number (1-8).
     */
    public int $section = 1;

    /**
     * Client ID.
     */
    public int $clientId = 0;

    /**
     * Certified role.
     */
    public ?string $certifiedRole = null;

    /**
     * Application status.
     *
     * Values: PendingReview, Complete, Saved, Rejected, Resubmitted, InProgress, Submitted
     */
    public ?string $status = null;

    /**
     * Applicant information.
     */
    public ?ApplicantInformation $applicantInformation = null;

    /**
     * Business entity details.
     */
    public ?BusinessEntityDetails $businessEntityDetails = null;

    /**
     * Business identity details.
     */
    public ?BusinessIdentityDetails $businessIdentityDetails = null;

    /**
     * Consumer protection contacts.
     */
    public ?ConsumerProtectionContacts $consumerProtectionContacts = null;

    /**
     * Business agent of record.
     */
    public ?BusinessAgentOfRecord $businessAgentOfRecord = null;

    /**
     * References.
     */
    public ?References $references = null;

    /**
     * Display identity information.
     */
    public ?GetNewDisplayIdentity $displayIdentityInformation = null;

    /**
     * Certification.
     */
    public ?RegisterCertificate $registerCertificate = null;

    /**
     * Uploaded documents.
     *
     * @var array<int, DocumentDTO>|null
     */
    public ?array $documents = null;

    /**
     * Creation timestamp.
     */
    public ?DateTimeImmutable $createdDate = null;

    /**
     * Last update timestamp.
     */
    public ?DateTimeImmutable $lastUpdatedDate = null;

    /**
     * Last updated by.
     */
    public ?string $lastUpdatedBy = null;

    /**
     * Enterprise ID.
     */
    public ?string $eid = null;

    /**
     * Business ID.
     */
    public ?string $businessId = null;

    /**
     * Verification attempt count.
     */
    public ?int $verificationCount = null;

    /**
     * {@inheritDoc}
     */
    public static function fromArray(array $data): static
    {
        $instance = new static;

        $instance->numhubEntityId = $data['numhubEntityId'] ?? null;
        $instance->applicationId = $data['applicationId'] ?? null;
        $instance->section = (int) ($data['section'] ?? 1);
        $instance->clientId = (int) ($data['clientId'] ?? 0);
        $instance->certifiedRole = $data['certifiedRole'] ?? null;
        $instance->status = $data['status'] ?? null;
        $instance->eid = $data['eid'] ?? null;
        $instance->businessId = $data['businessId'] ?? null;
        $instance->lastUpdatedBy = $data['lastUpdatedBy'] ?? null;
        $instance->verificationCount = isset($data['verificationCount']) ? (int) $data['verificationCount'] : null;

        // Parse dates
        if (! empty($data['createdDate'])) {
            $instance->createdDate = new DateTimeImmutable($data['createdDate']);
        }
        if (! empty($data['lastUpdatedDate'])) {
            $instance->lastUpdatedDate = new DateTimeImmutable($data['lastUpdatedDate']);
        }

        // Parse nested objects
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
            $instance->displayIdentityInformation = GetNewDisplayIdentity::fromArray($data['displayIdentityInformation']);
        }

        if (isset($data['registerCertificate']) && is_array($data['registerCertificate'])) {
            $instance->registerCertificate = RegisterCertificate::fromArray($data['registerCertificate']);
        }

        if (isset($data['documents']) && is_array($data['documents'])) {
            $instance->documents = array_map(
                fn (array $doc) => DocumentDTO::fromArray($doc),
                $data['documents']
            );
        }

        return $instance;
    }

    /**
     * Check if application is approved/complete.
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === 'Complete';
    }

    /**
     * Check if application is pending review.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['PendingReview', 'Submitted', 'InProgress'], true);
    }

    /**
     * Check if application was rejected.
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === 'Rejected';
    }

    /**
     * Get company name from business entity details.
     *
     * @return string|null
     */
    public function getCompanyName(): ?string
    {
        return $this->businessEntityDetails?->legalBusinessName;
    }
}
