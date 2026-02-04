# NumHub BrandControl API - Data Models Reference

> Generated from Swagger: https://brandidentity-api.numhub.com/docs/swagger.json
> Date: 2026-02-04
> API Version: V1 (rel. 231011)

---

## Table of Contents

1. [Authentication Models](#authentication-models)
2. [Application Models](#application-models)
3. [Display Identity Models](#display-identity-models)
4. [Deal Models](#deal-models)
5. [Report Models](#report-models)
6. [Supporting Models](#supporting-models)
7. [Enum Values](#enum-values)

---

## Authentication Models

### AccessModel (Token Response)

Returned from `POST /api/v1/authorize/token`

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| accessToken | string | ✓ | JWT access token (24h expiry) |
| tokenType | string | ✓ | Always "Bearer" |
| expiresIn | int32 | | Seconds until expiry (86400) |
| user | UserModelDto | | User details |
| userRoles | UserRolesDto[] | ✓ | Assigned roles |
| userClients | UserClientsDto[] | ✓ | Accessible clients |

### UserModelDto

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| userId | int32 | ✓ | Unique user ID |
| userName | string | ✓ | Username |
| firstName | string | ✓ | First name |
| lastName | string | ✓ | Last name |
| email | string | ✓ | Email address |
| phoneNumber | string | ✓ | Phone number |
| sendTextMessage | bool | ✓ | SMS notifications enabled |
| requireMultiFactorAuth | bool | ✓ | MFA required |
| enabled | bool | ✓ | Account active |
| lastLoginDate | datetime | ✓ | Last login timestamp |
| insertDate | datetime | ✓ | Account creation date |
| insertName | string | ✓ | Created by |
| modifiedDate | datetime | ✓ | Last modified date |
| modifiedName | string | ✓ | Modified by |
| isPasswordGenerated | bool | | Auto-generated password |

### UserRolesDto

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| userId | int32 | ✓ | User ID |
| roleType | RoleType enum | | Role type |
| description | string | ✓ | Role description |
| assigned | bool | ✓ | Is role assigned |

### UserClientsDto

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| userId | int32 | ✓ | User ID |
| clientId | int32 | ✓ | Client ID |
| clientName | string | ✓ | Client name |
| respOrgId | string | ✓ | RespOrg identifier |
| assigned | bool | ✓ | Is assigned |
| hasBcid | bool | ✓ | Has BCID enabled |
| isOsp | bool | ✓ | Is OSP account |
| ospId | string | ✓ | OSP identifier |

---

## Application Models

### SaveBCApplicationModel (Create Request)

Used for `POST /api/v1/application`

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| applicantInformation | ApplicantInformation | | Applicant contact info |
| businessEntityDetails | BusinessEntityDetails | | Business details |
| businessIdentityDetails | BusinessIdentityDetails | | Business identifiers |
| consumerProtectionContacts | ConsumerProtectionContacts | | Consumer support info |
| businessAgentOfRecord | BusinessAgentOfRecord | | Legal representative |
| references | References | | Business references |
| displayIdentityInformation | DisplayIdentityInfo | | Caller ID info |
| registerCertificate | RegisterCertificate | | Certification |
| section | int32 | | Current section (1-8) |
| status | SaveStatus enum | | "Saved" or "Submit" |

### UpdateBCApplicationModel (Update Request)

Used for `PUT /api/v1/application/{NumhubEntityId}`

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| section | int32 | | Current section |
| applicationId | string | ✓ | Application ID |
| status | SaveStatus enum | | "Saved" or "Submit" |
| applicantInformation | ApplicantInformation | | Applicant info |
| businessEntityDetails | BusinessEntityDetails | | Business details |
| businessIdentityDetails | BusinessIdentityDetails | | Business identifiers |
| consumerProtectionContacts | ConsumerProtectionContacts | | Consumer support |
| businessAgentOfRecord | BusinessAgentOfRecord | | Legal representative |
| references | References | | Business references |
| displayIdentityInformation | DisplayIdentity | | Caller ID info |
| registerCertificate | RegisterCertificate | | Certification |
| modifiedSections | string[] | ✓ | Changed sections |

### GetBCApplicationDto (Application Response)

Returned from `GET /api/v1/application/{NumhubEntityId}`

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| numhubEntityId | uuid | | Entity UUID |
| applicationId | string | ✓ | Application ID |
| section | int32 | | Current section |
| clientId | int32 | | Client ID |
| certifiedRole | string | ✓ | Certified role |
| status | ApplicationStatus enum | ✓ | Application status |
| applicantInformation | ApplicantInformation | | Applicant info |
| businessEntityDetails | BusinessEntityDetails | | Business details |
| businessIdentityDetails | BusinessIdentityDetails | | Business identifiers |
| consumerProtectionContacts | ConsumerProtectionContacts | | Consumer support |
| businessAgentOfRecord | BusinessAgentOfRecord | | Legal representative |
| references | References | | Business references |
| displayIdentityInformation | GetNewDisplayIdentity | | Caller ID info |
| registerCertificate | RegisterCertificate | | Certification |
| documents | Documents[] | ✓ | Uploaded documents |
| createdDate | datetime | ✓ | Creation date |
| lastUpdatedDate | datetime | ✓ | Last update |
| lastUpdatedBy | string | ✓ | Updated by |
| eid | string | ✓ | Enterprise ID |
| businessId | string | ✓ | Business ID |
| verificationCount | int32 | ✓ | Verification attempts |

### CreateEntityResponseModel

Returned from application create/update

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| numhubEntityId | uuid | | New/updated entity UUID |
| applicationId | string | ✓ | Application ID |
| eid | string | ✓ | Enterprise ID |

### GetVettedApplicationDto (Vetting Report)

Returned from `GET /api/v1/application/vetting-report/{NumhubEntityId}`

Extends GetBCApplicationDto with vetting review fields on each section.

---

## Nested Application Models

### ApplicantInformation

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| firstName | string | ✓ | First name |
| lastName | string | ✓ | Last name |
| emailAddress | string | ✓ | Email |
| phoneNumber | string | ✓ | Phone |
| ospIds | string[] | ✓ | Associated OSP IDs |
| isVerified | bool | | Email verified |

### BusinessEntityDetails

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| legalBusinessName | string | ✓ | Legal name |
| businessWebsite | string | ✓ | Website URL |
| primaryBusinessAddress | string | ✓ | Street address |
| city | string | ✓ | City |
| state | string | ✓ | State code |
| phone | string | ✓ | Business phone |
| originatingServiceProvider | string | ✓ | OSP name |
| zipCode | string | ✓ | ZIP code |

### BusinessIdentityDetails

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| federalEmployerIdNumber | string | ✓ | EIN/FEIN |
| dunAndBradstreetNumber | string | ✓ | D-U-N-S number |
| stateCorporateRegistrationNumber | string | ✓ | State registration |
| stateProfessionalLicenseNumber | string | ✓ | Professional license |
| primaryBusinessDomainSicCode | string | ✓ | SIC code |

### ConsumerProtectionContacts

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| consumerComplaintEmail | string | ✓ | Complaint email |
| consumerComplaintPhoneNumber | string | ✓ | Complaint phone |
| dataPrivacyWebsiteUrl | string | ✓ | Privacy policy URL |

### BusinessAgentOfRecord

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| institutionName | string | ✓ | Institution name |
| streetAddress | string | ✓ | Street address |
| city | string | ✓ | City |
| state | string | ✓ | State |
| zipCode | string | ✓ | ZIP code |
| contactName | string | ✓ | Contact name |
| phoneNumber | string | ✓ | Phone |
| emailAddress | string | ✓ | Email |

### References

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| referenceOneName | string | ✓ | Reference 1 name |
| referenceOneTitle | string | ✓ | Reference 1 title |
| referenceOnePhone | string | ✓ | Reference 1 phone |
| referenceOneEmail | string | ✓ | Reference 1 email |
| referenceTwoName | string | ✓ | Reference 2 name |
| referenceTwoTitle | string | ✓ | Reference 2 title |
| referenceTwoPhone | string | ✓ | Reference 2 phone |
| referenceTwoEmail | string | ✓ | Reference 2 email |
| financialInstitutionName | string | ✓ | Bank name |
| streetAddress | string | ✓ | Bank address |
| city | string | ✓ | Bank city |
| state | string | ✓ | Bank state |
| zipCode | string | ✓ | Bank ZIP |
| contactName | string | ✓ | Bank contact |
| phoneNumber | string | ✓ | Bank phone |
| emailAddress | string | ✓ | Bank email |

### RegisterCertificate

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| fullName | string | ✓ | Certifier name |
| date | datetime | ✓ | Certification date |
| certified | bool | | Is certified |
| isFeeChecked | bool | | Fee acknowledged |

---

## Display Identity Models

### DisplayIdentityByIdResponse

Returned from `GET /api/v1/applications/newidentities/{NumhubIdentityId}`

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| callerName | string | ✓ | Display name (15-32 chars) |
| callReason | string | ✓ | Call reason text |
| logoUrl | string | ✓ | Logo image URL |
| dirId | string | ✓ | Directory ID |
| activationDate | datetime | ✓ | Activation date |
| deactivationDate | datetime | ✓ | Deactivation date |
| modifiedDate | datetime | ✓ | Last modified |
| companyName | string | ✓ | Company name |
| eid | string | ✓ | Enterprise ID |
| numhubEntityId | uuid | | Entity UUID |
| numhubIdentityId | uuid | | Identity UUID |
| status | string | ✓ | Identity status |
| additionalTnsPath | string | ✓ | Bulk TN file path |
| isFeeChecked | bool | | Fee acknowledged |
| phoneNumbers | string[] | ✓ | Associated phone numbers |

### NewDisplayIdentityInfoUpdateRequestDto

Used for `PUT /api/v1/applications/updatedisplayidentity`

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| numhubEntityId | uuid | | Entity UUID |
| numhubIdentityId | uuid | | Identity UUID |
| callerName | string | ✓ | Display name |
| dirId | string | ✓ | Directory ID (max 36 chars) |
| logoUrl | string | ✓ | Logo URL |
| callReason | string | ✓ | Call reason |
| status | string | ✓ | Status |
| isDeactivation | bool | | Deactivation request |
| tnAdded | string[] | ✓ | Phone numbers to add |
| tnRemoved | string[] | ✓ | Phone numbers to remove |
| additionalTnsPath | string | ✓ | Bulk TN file path |
| isFeeChecked | bool | | Fee acknowledged |
| displayIdentitiesChangeRequest | DisplayIdentitiesChangeRequestDto | | Change request |

### NewDisplayIdentityDto (List Response Item)

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| companyName | string | ✓ | Company name |
| dirId | string | ✓ | Directory ID |
| logoUrl | string | ✓ | Logo URL |
| status | string | ✓ | Status |
| telePhoneCount | int32 | | Phone number count |
| numhubIdentityId | uuid | | Identity UUID |
| modifiedDate | datetime | ✓ | Last modified |
| callerName | string | ✓ | Display name |
| numhubEntityId | uuid | | Entity UUID |
| changeRequestId | int64 | | Pending change request ID |
| eid | string | ✓ | Enterprise ID |

---

## Deal Models

### BrandControlDeals

Used for `POST /api/v1/deals` and `PUT /api/v1/deals/{dealId}`

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| clientId | int32 | | Client ID |
| dealID | int32 | | Deal ID |
| dealName | string | ✓ | Deal name |
| numhubInternalId | string | ✓ | NumHub internal ID |
| vettingFee | double | | Vetting fee |
| retailFee | double | | Retail fee per delivery |
| platformFee | double | | Platform fee per identity |
| callVolume | double | | Expected call volume |
| customerEmailAddress | string | ✓ | Customer email |
| operationsEmail | string | ✓ | Operations email |
| customerPrimaryContactFirstName | string | ✓ | Contact first name |
| customerPrimaryContactLastName | string | ✓ | Contact last name |
| customerPhoneNumber | string | ✓ | Customer phone |
| customerAddress | string | ✓ | Address |
| customerCity | string | ✓ | City |
| customerState | string | ✓ | State |
| customerZipCode | string | ✓ | ZIP code |
| parentId | string | ✓ | Parent ID |
| serviceProvider | string | ✓ | Service provider |
| ospId | string | ✓ | OSP ID |
| bcidEidNotificationEmails | string | ✓ | Notification emails |
| customerLastLoginDate | datetime | ✓ | Last login |
| respOrgId | string | ✓ | RespOrg ID |
| roleType | RoleType enum | ✓ | "Enterprise" or "BPO" |
| orgType | OrgType enum | ✓ | Organization type |

### DealListResponse

Returned from `GET /api/v1/deals`

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| dealsList | DealsResponseDto[] | ✓ | Deal list |
| metaData | Pagination | | Pagination info |

### DealsResponseDto

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| clientId | int32 | | Client ID |
| dealId | int32 | | Deal ID |
| clientName | string | ✓ | Client name |
| customerEmailAddress | string | ✓ | Customer email |
| customerEmail | string | ✓ | Customer email (alt) |
| customerLastLoginTimestamp | datetime | ✓ | Last login |
| enabled | bool | | Is enabled |
| modifiedDate | datetime | | Modified date |
| modifiedBy | string | ✓ | Modified by |
| respOrgId | string | ✓ | RespOrg ID |
| hasBCID | bool | | Has BCID |
| bcidServiceProvider | string | ✓ | Service provider |
| bcidParentId | string | ✓ | Parent ID |
| numhubInternalId | string | ✓ | NumHub internal ID |
| bcidWhiteLabeledUrl | string | ✓ | White label URL |
| bcidOspId | string | ✓ | OSP ID |
| bcidIsOsp | bool | | Is OSP |
| bcidEidNotificationEmails | string | ✓ | Notification emails |
| bcidRoleType | string | ✓ | Role type |
| bcidOrgType | string | ✓ | Org type |
| fullName | string | ✓ | Full name (readonly) |

### OspDefaultFeeModel

Used for `POST/PUT /api/v1/deals/fees/{ospId}`

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| vettingFee | double | | Vetting fee amount |
| retailFeePerConfirmedDelivery | double | | Per-delivery fee |
| platformFeePerIdentity | double | | Per-identity fee |
| applyToDealRegistration | bool | | Apply to new deals |

### OspDefaultFeeEntity

Returned from `GET /api/v1/deals/fees/{ospId}`

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| id | int32 | | Fee ID |
| ospId | string | ✓ | OSP ID |
| clientId | int32 | | Client ID |
| vettingFee | double | | Vetting fee |
| retailFeePerConfirmedDelivery | double | | Per-delivery fee |
| platformFeePerIdentity | double | | Per-identity fee |
| applyToDealRegistration | bool | | Apply to new deals |

---

## Report Models

### BCIDSettlementLogResponseDto

Returned from `GET /api/v1/confirmationReports/settlementReports`

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| id | uuid | | Log ID |
| inserted_Timestamp | datetime | ✓ | Insert timestamp |
| eId | string | ✓ | Enterprise ID |
| bpoId | string | ✓ | BPO ID |
| dest | string | ✓ | Destination number |
| vaid | string | ✓ | VA ID |
| claims_Passed | string | ✓ | Claims passed |
| tspId | string | ✓ | TSP ID |
| tsp_Fee | double | | TSP fee |
| orig | string | ✓ | Originating number |
| ospId | string | ✓ | OSP ID |
| tsp_Name | string | ✓ | TSP name |
| dirId | string | ✓ | Directory ID |
| claims_Requested | string | ✓ | Claims requested |
| said | string | ✓ | SA ID |
| iat | int64 | | IAT timestamp |
| oaid | string | ✓ | OA ID |
| iatTimeStamp | datetime | ✓ | IAT as datetime |
| bcidInternalId | string | ✓ | BCID internal ID |
| sig | string | ✓ | Signature |
| crn | string | ✓ | Call reference number |
| metadata | string | ✓ | Additional metadata |

### ReportResponseDto (Status Report)

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| entities | EntityDetails[] | ✓ | Entity list |
| totalCount | int32 | | Total count |
| statusCount | object | ✓ | Count by status |

### VettingStatusCountResponse

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| application | StatusCountItem[] | ✓ | Application counts |
| dis | StatusCountItem[] | ✓ | Display identity counts |
| disChangeRequest | OspStatusSummary[] | ✓ | Change request counts |

---

## Supporting Models

### Pagination

| Field | Type | Description |
|-------|------|-------------|
| pageSize | int32 | Items per page |
| currentPage | int32 | Current page number |
| totalCount | int32 | Total items |
| totalPages | int32 | Total pages |

### BrandControlApiResponse

Standard API wrapper response.

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| success | bool | | Operation success |
| message | string | ✓ | Response message |
| result | any | ✓ | Response data |

### Documents

| Field | Type | Nullable | Description |
|-------|------|----------|-------------|
| documentId | uuid | | Document UUID |
| name | string | ✓ | File name |
| url | string | ✓ | Download URL |
| createdDate | datetime | ✓ | Upload date |
| modifiedDate | datetime | ✓ | Modified date |
| createdBy | string | ✓ | Uploaded by |

---

## Enum Values

### ApplicationStatus

| Value | Description |
|-------|-------------|
| PendingReview | Awaiting review |
| Complete | Approved and complete |
| Saved | Draft saved |
| Rejected | Application rejected |
| Resubmitted | Resubmitted after rejection |
| InProgress | Review in progress |
| Submitted | Submitted for review |

### SaveStatus (Request)

| Value | Description |
|-------|-------------|
| Saved | Save as draft (status=1) |
| Submit | Submit for processing (status=2) |

### RoleType (Deals)

| Value | Description |
|-------|-------------|
| None | Not specified |
| Enterprise | Enterprise customer |
| BPO | Business Process Outsourcer |

### OrgType

| Value | Description |
|-------|-------------|
| None | Not specified |
| CommercialEnterprise | Commercial business |
| GovtPublicService | Government/public service |
| CharityNonProfit | Charity/non-profit |

### UserRoleType

| Value | Description |
|-------|-------------|
| GlobalAdmin | Global administrator |
| OSPAdmin | OSP administrator |
| Enterprise | Enterprise user |
| EditUsers | Can edit users |
| ViewTemplates | Can view templates |
| ... | (see API docs for full list) |

### VettingReview

| Value | Description |
|-------|-------------|
| Pass | Passed vetting |
| Fail | Failed vetting |
| Flag | Flagged for review |

---

## API Request Headers

All authenticated requests require:

```
Authorization: Bearer {accessToken}
client-id: {clientId}
X-Auth-Scheme: ATLAASROPG
Content-Type: application/json
Accept: application/json
```

---

## Rate Limiting

- **Limit**: 100 requests per minute
- **Header**: `requests-remaining` shows remaining quota
- **Exceeded**: Returns 429 Too Many Requests

---

*Last updated: 2026-02-04*
