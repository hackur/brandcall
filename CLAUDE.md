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
| **CNAM** | Caller Name - the 32-char display name |
| **Attestation** | Verification level (A=full, B=partial, C=gateway) |
| **VPU** | Voice Pick Up - answer rate metric |
| **Number Burn** | When a phone number gets spam-labeled and becomes unusable |
| **Remediation** | Process of removing spam labels from numbers |
| **BCID** | Branded Calling ID - CTIA's industry-governed ecosystem |

Full glossary: `docs/INDUSTRY-TERMS.md`

---

## Competitive Landscape

| Competitor | Focus | Our Advantage |
|------------|-------|---------------|
| **Hiya** | Consumer spam blocking | We're business-focused, transparent |
| **First Orion** | Enterprise branded calling | We serve SMB too, modern API |
| **Numeracle** | Reputation management | We add fraud prevention |
| **TNS** | Enterprise authentication | We're developer-friendly |

Full analysis: `docs/FEATURES.md`

---

## Technical Architecture

### Stack
- **Backend**: Laravel 12 + PHP 8.2
- **Customer Frontend**: Inertia.js + React 19 + TypeScript
- **Admin Panel**: Filament 3
- **Database**: SQLite (dev) / MySQL (prod)
- **Auth**: Sanctum + Spatie Permission (RBAC)
- **External**: NumHub BrandControl API

### Multi-Tenancy
Row-level security via `TenantScope` global scope. Every tenant-scoped model auto-filters by `tenant_id`.

```php
// This automatically filters by current tenant
$brands = Brand::all();

// To bypass (admin only)
Brand::withoutGlobalScope(TenantScope::class)->get();
```

### Roles & Permissions
- **super-admin**: Platform-wide access, Filament admin panel
- **owner**: Full tenant access, billing management
- **admin**: Most tenant features, no billing
- **member**: Read + make calls only

Full RBAC: `database/seeders/RolesAndPermissionsSeeder.php`

---

## Code Standards

### Non-Negotiable
- **SOLID, DRY, KISS** - Always
- **PHPDoc on all methods** - Full `@param`, `@return`, `@throws`
- **Strict types** - `declare(strict_types=1)` in all PHP files
- **Laravel Pint** - Run `composer lint` before committing
- **Larastan Level 6** - Run `composer analyse` before committing

### Commands
```bash
composer dev        # Start all dev servers
composer lint       # Fix code style
composer analyse    # Run static analysis
composer quality    # Both lint:check + analyse
composer smoke      # Run smoke tests (20 page tests)
composer test       # Run all tests
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

## Key Files

| File | Purpose |
|------|---------|
| `docs/FEATURES.md` | Competitor matrix, feature roadmap |
| `docs/INDUSTRY-TERMS.md` | Industry glossary, business concepts |
| `docs/LEADS-STRATEGY.md` | GTM strategy, pricing, sales playbook |
| `docs/DEVELOPER.md` | Technical documentation |
| `docs/USER-GUIDE.md` | End-user guide with screenshots |
| `app/Scopes/TenantScope.php` | Multi-tenancy implementation |
| `database/seeders/RolesAndPermissionsSeeder.php` | RBAC definitions |

---

## Test Users

All passwords: `password`

| Email | Role | Access |
|-------|------|--------|
| admin@brandcall.com | super-admin | Filament admin panel |
| owner@example.com | owner | Customer dashboard (tenant owner) |
| admin@example.com | admin | Customer dashboard (tenant admin) |
| member@example.com | member | Customer dashboard (limited) |

---

## Next Implementation Phases

### Phase 2: NumHub Integration (Next)
- [ ] NumHub service class (`app/Services/NumHubService.php`)
- [ ] Brand registration with NumHub API
- [ ] Call initiation endpoint
- [ ] Webhook handling for call events
- [ ] Call logging to database

### Phase 3: Analytics
- [ ] Answer rate tracking
- [ ] Dashboard charts (Chart.js or Recharts)
- [ ] Report exports (CSV/Excel)
- [ ] Campaign management

### Phase 4: Enterprise
- [ ] SSO/SAML integration
- [ ] Advanced audit logs
- [ ] Custom SLAs
- [ ] White-label support

---

## Decision Log

| Date | Decision | Rationale |
|------|----------|-----------|
| 2026-01-31 | Spatie Permission for RBAC | Community standard, well-maintained |
| 2026-01-31 | Filament 3 for admin | Modern, community-accepted, rapid development |
| 2026-01-31 | TenantScope pattern | Simple row-level security, Laravel-native |
| 2026-01-31 | Per-brand API keys | Granular security, easier key rotation |
| 2026-01-31 | NumHub over direct carriers | Aggregated carrier access, faster integration |

---

## Important Links

- NumHub API Docs: https://numhub.com/docs (needs API credentials)
- Hiya State of the Call 2025: Industry research reference
- CTIA BCID: https://www.ctia.org/branded-calling

---

*Last updated: 2026-01-31*
