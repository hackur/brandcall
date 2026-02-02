# CLAUDE.md - BrandCall Project Context

> **Read this file first when working on BrandCall.**
> This contains critical business context, technical decisions, and guidelines.

---

## Business Overview

**BrandCall** is a Branded Caller ID SaaS platform that enables businesses to display their company name, logo, and call reason on outbound calls.

### The Problem We Solve
- 80% of unidentified calls go unanswered (Hiya 2025)
- Legitimate business calls are indistinguishable from spam
- Answer rates for unknown numbers are ~20%
- Businesses waste money on outbound calling that doesn't connect

### Our Solution
- Verified branded caller ID (name, logo, call reason)
- STIR/SHAKEN compliance via NumHub
- Reputation management to prevent spam labels
- Fraud prevention to protect the ecosystem

### Target Market (Priority Order)
1. **Healthcare** - Appointment reminders, test results, telehealth
2. **Insurance** - Open enrollment, renewals, claims callbacks
3. **Financial Services** - Fraud alerts, collections, customer service
4. **Contact Centers/BPOs** - White-label branded calling for clients
5. **Home Services** - Appointment confirmations, technician ETA

---

## Key Industry Terminology

These terms appear throughout the codebase and docs. Know them.

| Term | Meaning |
|------|---------|
| **STIR/SHAKEN** | FCC-mandated call authentication (cryptographic signing) |
| **RCD** | Rich Call Data - name, logo, call reason displayed on phones |
| **CNAM** | Caller Name - the 15-32 char display name |
| **Attestation** | Verification level (A=full, B=partial, C=gateway) |
| **VPU** | Voice Pick Up - answer rate metric |
| **Number Burn** | When a phone number gets spam-labeled and becomes unusable |
| **Remediation** | Process of removing spam labels from numbers |
| **BCID** | Branded Calling ID - CTIA's industry-governed ecosystem |
| **KYC** | Know Your Customer - identity verification for compliance |
| **LOA** | Letter of Authorization - phone number ownership proof |

Full glossary: `docs/INDUSTRY-TERMS.md`

---

## Technical Architecture

### Stack
- **Backend**: Laravel 12 + PHP 8.4
- **Customer Frontend**: Inertia.js + React 19 + TypeScript
- **Admin Panel**: Filament 3
- **Database**: SQLite (dev) / MySQL (prod)
- **Cache/Sessions**: Redis
- **Auth**: Sanctum + Spatie Permission (RBAC)
- **File Storage**: Spatie Media Library
- **External**: NumHub BrandControl API (pending credentials)

### Directory Structure
```
app/
├── Contracts/          # Interfaces (VoiceProvider, etc.)
├── Facades/            # Laravel Facades (Voice)
├── Filament/           # Admin panel resources
│   ├── Resources/      # CRUD resources (User, Document, etc.)
│   └── Widgets/        # Dashboard widgets
├── Http/
│   ├── Controllers/    # Web controllers
│   │   ├── Admin/      # Admin-only controllers
│   │   ├── Api/        # API controllers
│   │   └── Auth/       # Authentication (Breeze)
│   ├── Middleware/     # Custom middleware
│   └── Requests/       # Form request validation
├── Models/             # Eloquent models
├── Providers/          # Service providers
├── Scopes/             # Global scopes (TenantScope)
└── Services/           # Business logic
    └── Voice/          # Voice provider drivers
```

### Multi-Tenancy
Row-level security via `TenantScope` global scope. Every tenant-scoped model auto-filters by `tenant_id`.

```php
// This automatically filters by current tenant
$brands = Brand::all();

// To bypass (admin only)
Brand::withoutGlobalScope(TenantScope::class)->get();
```

### Voice Provider Architecture
Driver pattern for multiple telephony providers:

```php
// app/Contracts/VoiceProvider.php - Interface
// app/Services/Voice/VoiceManager.php - Manager class
// app/Services/Voice/NumHubDriver.php - Primary driver
// app/Services/Voice/TwilioDriver.php - Fallback driver
// app/Services/Voice/NullDriver.php - Testing mock

// Usage via Facade
Voice::call($brand, $from, $to, $callReason);
```

---

## User Onboarding Flow

### Status Progression
```
pending → verified → approved
```

1. **Registration** (status: pending)
   - User creates account
   - Receives verification email
   
2. **Email Verification**
   - User clicks verification link
   - Can now access onboarding dashboard
   
3. **Company Profile**
   - User completes company information
   - Industry, call volume, use case
   
4. **Document Upload** (KYC)
   - Business license, Tax ID, Government ID
   - Type-specific MIME validation
   - Thumbnails auto-generated for images
   
5. **KYC Submission** (status: verified)
   - User submits documents for review
   - Support ticket created automatically
   
6. **Admin Approval** (status: approved)
   - Admin reviews documents in Filament
   - Approves or rejects with notes
   - User gains full platform access

### Document Types
| Type | Allowed Formats | Purpose |
|------|-----------------|---------|
| business_license | PDF, JPG, PNG | Business verification |
| tax_id | PDF, JPG, PNG | EIN/Tax verification |
| drivers_license | PDF, JPG, PNG | Identity verification |
| government_id | PDF, JPG, PNG | Identity verification |
| loa | PDF only | Phone number authorization |
| articles_incorporation | PDF only | Legal entity verification |
| w9 | PDF only | IRS tax form |
| utility_bill | PDF, JPG, PNG | Address verification |

---

## Roles & Permissions

### Role Hierarchy
- **super-admin**: Platform-wide access, Filament admin panel
- **owner**: Full tenant access, billing management
- **admin**: Most tenant features, no billing
- **member**: Read + make calls only

### Key Permission Checks
```php
// Admin panel access
$user->canAccessPanel($panel); // Checks hasRole('super-admin')

// Tenant ownership
$user->isOwner(); // Checks hasRole('owner')

// Admin capabilities
$user->isTenantAdmin(); // Checks owner OR admin role
```

Full RBAC: `database/seeders/RolesAndPermissionsSeeder.php`

---

## Code Standards

### Non-Negotiable
- **SOLID, DRY, KISS** - Always
- **PHPDoc on all public methods** - Full `@param`, `@return`, `@throws`
- **PSR-12 code style** - Laravel Pint enforces this
- **Type hints** - All parameters and return types
- **Laravel Pint** - Run `composer lint` before committing
- **Larastan** - Run `composer analyse` before committing

### PHPDoc Standards (PSR-5)
```php
/**
 * Brief one-line description.
 *
 * Longer description if needed. Can span multiple
 * lines and include examples.
 *
 * @param string $param Description of parameter
 * @param array<string, mixed> $options Optional parameters
 * @return array{success: bool, error?: string} Return description
 * @throws \InvalidArgumentException If param is invalid
 */
public function method(string $param, array $options = []): array
```

### Commands
```bash
composer dev        # Start all dev servers (Vite + Artisan serve)
composer lint       # Fix code style with Pint
composer lint:check # Check code style (CI)
composer analyse    # Run PHPStan static analysis
composer quality    # Both lint:check + analyse
composer smoke      # Run smoke tests (20 page tests)
composer test       # Run all tests

# Deployment (via Deployer)
composer deploy     # Zero-downtime deploy to production
composer rollback   # Rollback to previous release
composer releases   # List available releases
```

### Commit Convention
```
feat: Add new feature
fix: Bug fix
docs: Documentation only
style: Formatting, no logic change
refactor: Code change, no feature change
test: Add/update tests
chore: Maintenance, deps, config
```

---

## Key Files Reference

### Documentation
| File | Purpose |
|------|---------|
| `CLAUDE.md` | This file - project context |
| `docs/FEATURES.md` | Competitor matrix, feature roadmap |
| `docs/INDUSTRY-TERMS.md` | Industry glossary, business concepts |
| `docs/LEADS-STRATEGY.md` | GTM strategy, pricing, sales playbook |
| `docs/DEVELOPER.md` | Technical documentation |
| `docs/USER-GUIDE.md` | End-user guide with screenshots |
| `docs/DEPLOYMENT.md` | Server setup and deployment guide |
| `docs/TCPA-VIOLATIONS-GUIDE.md` | TCPA compliance content |
| `TODO.md` | Current task list and roadmap |

### Core Business Logic
| File | Purpose |
|------|---------|
| `app/Models/User.php` | User accounts, onboarding status |
| `app/Models/Document.php` | KYC document storage |
| `app/Models/Brand.php` | Branded caller ID profiles |
| `app/Models/Tenant.php` | Multi-tenant organizations |
| `app/Models/SupportTicket.php` | Customer support |
| `app/Contracts/VoiceProvider.php` | Voice API interface |
| `app/Services/Voice/VoiceManager.php` | Provider management |
| `app/Scopes/TenantScope.php` | Multi-tenancy implementation |

### Admin Panel (Filament)
| File | Purpose |
|------|---------|
| `app/Filament/Resources/UserResource.php` | User management |
| `app/Filament/Resources/DocumentResource.php` | KYC review |
| `app/Filament/Resources/SupportTicketResource.php` | Support tickets |
| `app/Providers/Filament/AdminPanelProvider.php` | Panel config |

---

## Test Users

All passwords: `password`

| Email | Role | Access |
|-------|------|--------|
| admin@brandcall.io | super-admin | Filament admin panel |
| owner@example.com | owner | Customer dashboard (tenant owner) |
| admin@example.com | admin | Customer dashboard (tenant admin) |
| member@example.com | member | Customer dashboard (limited) |

---

## Production Environment

### Server Details
- **IP**: 178.156.223.166
- **Provider**: Hetzner Cloud (CPX21)
- **Location**: Ashburn, VA (us-east)
- **Stack**: Ubuntu 24.04, Nginx, PHP 8.4, MariaDB, Redis

### Deployment
- **Method**: Deployer (zero-downtime via symlinks)
- **Releases kept**: 5
- **Storage**: Shared across releases
- **Command**: `composer deploy`

### URLs
- **Production**: https://brandcall.io
- **Admin Panel**: https://brandcall.io/admin

---

## Decision Log

| Date | Decision | Rationale |
|------|----------|-----------|
| 2026-02-02 | Spatie Media Library | Thumbnails, conversions, organized storage |
| 2026-02-02 | Redis for sessions | Stable across deployments, faster |
| 2026-02-02 | Deployer over Envoyer | Free, zero-downtime, rsync-based |
| 2026-01-31 | Spatie Permission for RBAC | Community standard, well-maintained |
| 2026-01-31 | Filament 3 for admin | Modern, rapid development |
| 2026-01-31 | TenantScope pattern | Simple row-level security |
| 2026-01-31 | NumHub over direct carriers | Aggregated carrier access |

---

## Blocked / Pending

| Feature | Waiting On |
|---------|------------|
| Email verification | Resend API key |
| Voice API | NumHub credentials |
| Payments | Stripe keys |

---

## Important Links

- NumHub API Docs: https://numhub.com/docs (needs credentials)
- Hiya State of the Call 2025: Industry research reference
- CTIA BCID: https://www.ctia.org/branded-calling
- Spatie Media Library: https://spatie.be/docs/laravel-medialibrary

---

*Last updated: 2026-02-02*
