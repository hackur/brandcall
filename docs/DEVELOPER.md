# BrandCall Developer Documentation

Technical documentation for developers working on or integrating with BrandCall.

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Project Structure](#project-structure)
3. [Database Schema](#database-schema)
4. [Multi-Tenancy](#multi-tenancy)
5. [Authentication & Authorization](#authentication--authorization)
6. [API Development](#api-development)
7. [Frontend Development](#frontend-development)
8. [Testing](#testing)
9. [Code Quality](#code-quality)
10. [Deployment](#deployment)

---

## Architecture Overview

BrandCall uses a modern Laravel stack with Inertia.js for the customer-facing SPA and Filament for the admin panel.

```
┌─────────────────────────────────────────────────────────────┐
│                        Frontend                              │
├──────────────────────────┬──────────────────────────────────┤
│   Customer Portal        │        Admin Panel               │
│   (Inertia + React)      │        (Filament)                │
│   /dashboard, /brands    │        /admin/*                  │
└──────────────────────────┴──────────────────────────────────┘
                           │
┌──────────────────────────┴──────────────────────────────────┐
│                     Laravel Backend                          │
├─────────────────────────────────────────────────────────────┤
│  Controllers  │  Models  │  Services  │  Middleware         │
│  (Inertia)    │  (ORM)   │  (NumHub)  │  (Auth, Tenant)     │
└─────────────────────────────────────────────────────────────┘
                           │
┌──────────────────────────┴──────────────────────────────────┐
│                     External Services                        │
├──────────────────────────┬──────────────────────────────────┤
│      NumHub API          │         Stripe                   │
│   (Branded Calling)      │       (Billing)                  │
└──────────────────────────┴──────────────────────────────────┘
```

### Tech Stack

| Layer | Technology | Purpose |
|-------|------------|---------|
| Backend | Laravel 12 | API, business logic, auth |
| Customer UI | Inertia.js + React | Single-page application |
| Admin UI | Filament 3 | Admin panel, CRUD |
| Database | SQLite/MySQL/PostgreSQL | Data persistence |
| Auth | Sanctum + Spatie Permission | API tokens + RBAC |
| External | NumHub BrandControl | Branded calling API |

---

## Project Structure

```
brandcall/
├── app/
│   ├── Filament/
│   │   ├── Resources/          # Admin CRUD resources
│   │   │   ├── BrandResource.php
│   │   │   ├── TenantResource.php
│   │   │   └── UserResource.php
│   │   └── Widgets/            # Dashboard widgets
│   │       └── StatsOverview.php
│   ├── Http/
│   │   ├── Controllers/        # Inertia controllers
│   │   │   ├── BrandController.php
│   │   │   └── DashboardController.php
│   │   └── Middleware/
│   ├── Models/                 # Eloquent models
│   │   ├── Brand.php
│   │   ├── Tenant.php
│   │   └── User.php
│   ├── Scopes/
│   │   └── TenantScope.php     # Multi-tenancy scope
│   └── Services/               # Business logic (NumHub, etc.)
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── RolesAndPermissionsSeeder.php
│       └── InitialUsersSeeder.php
├── resources/
│   └── js/
│       ├── Components/         # React components
│       ├── Layouts/            # Page layouts
│       └── Pages/              # Inertia pages
│           ├── Dashboard.tsx
│           ├── Brands/
│           └── Welcome.tsx
├── routes/
│   └── web.php                 # Web routes (Inertia)
├── tests/
│   └── Feature/
│       └── SmokeTest.php       # Page smoke tests
├── docs/                       # Documentation
│   ├── screenshots/            # UI screenshots
│   ├── FEATURES.md
│   ├── USER-GUIDE.md
│   └── DEVELOPER.md
├── pint.json                   # Linter config
└── phpstan.neon                # Static analysis config
```

---

## Database Schema

### Core Tables

```sql
-- Tenants (customer organizations)
tenants
├── id
├── name                    -- Company name
├── email                   -- Primary contact
├── slug                    -- URL identifier
├── stripe_customer_id      -- Billing
├── subscription_tier       -- starter/growth/enterprise
├── monthly_call_limit      -- NULL = unlimited
├── settings (JSON)
├── created_at
├── updated_at
└── deleted_at              -- Soft deletes

-- Users
users
├── id
├── tenant_id (FK → tenants) -- NULL for platform admins
├── name
├── email
├── password
├── email_verified_at
└── timestamps

-- Brands (Branded Caller IDs)
brands
├── id
├── tenant_id (FK → tenants)
├── name                    -- Internal name
├── slug                    -- API identifier
├── display_name            -- CNAM (32 chars max)
├── call_reason             -- Default Rich Call Data
├── logo_path               -- Storage path
├── status                  -- draft/pending_vetting/active/suspended
├── numhub_brand_id         -- External ID
├── api_key                 -- Per-brand API key
└── timestamps

-- Call Logs
call_logs
├── id
├── tenant_id (FK)
├── brand_id (FK)
├── call_id                 -- External call ID
├── from_number
├── to_number
├── call_reason
├── status                  -- initiated/answered/completed/failed
├── duration_seconds
├── cost
├── attestation_level       -- A/B/C
├── metadata (JSON)
└── timestamps
```

### Relationships

```
Tenant
├── hasMany → Users
├── hasMany → Brands
├── hasMany → CallLogs
└── hasMany → UsageRecords

Brand
├── belongsTo → Tenant
├── hasMany → BrandPhoneNumbers
└── hasMany → CallLogs

User
├── belongsTo → Tenant
└── belongsToMany → Roles (Spatie)
```

---

## Multi-Tenancy

BrandCall uses row-level security via `TenantScope`.

### How It Works

1. **TenantScope** is a global scope that filters queries by `tenant_id`
2. Applied automatically to tenant-scoped models (Brand, CallLog, etc.)
3. Requires `current_tenant` to be bound in the container

### Setting Tenant Context

```php
// In controllers/middleware
$tenant = $request->user()->tenant;
app()->instance('current_tenant', $tenant);

// Now all queries are automatically scoped
$brands = Brand::all(); // Only this tenant's brands
```

### Bypassing Tenant Scope

For admin operations that need cross-tenant access:

```php
// Single query
Brand::withoutGlobalScope(TenantScope::class)->get();

// All global scopes
Brand::withoutGlobalScopes()->get();
```

### Adding Tenant Scope to Models

```php
use App\Scopes\TenantScope;

class Brand extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }
}
```

---

## Authentication & Authorization

### Authentication Stack

- **Customer Portal**: Laravel session auth (via Breeze)
- **Admin Panel**: Filament auth with `canAccessPanel()` check
- **API**: Laravel Sanctum tokens (per-brand API keys)

### Role-Based Access Control

Using Spatie Laravel Permission:

```php
// Check role
$user->hasRole('super-admin');
$user->hasRole('owner');

// Check permission
$user->can('create brands');
$user->can('manage billing');

// In controllers
$this->authorize('create', Brand::class);
```

### Roles Defined

```php
// In RolesAndPermissionsSeeder
$superAdmin = Role::create(['name' => 'super-admin']);
$owner = Role::create(['name' => 'owner']);
$admin = Role::create(['name' => 'admin']);
$member = Role::create(['name' => 'member']);
```

### Admin Panel Access

```php
// User model
public function canAccessPanel(Panel $panel): bool
{
    if ($panel->getId() === 'admin') {
        return $this->hasRole('super-admin');
    }
    return true;
}
```

---

## API Development

### Route Structure

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    Route::prefix('brands/{brand:slug}')->group(function () {
        Route::post('/calls', [CallController::class, 'store']);
        Route::get('/calls', [CallController::class, 'index']);
        Route::get('/analytics', [AnalyticsController::class, 'show']);
    });
});
```

### Authentication

API requests use brand-specific API keys:

```php
// Middleware
public function handle($request, Closure $next)
{
    $apiKey = $request->bearerToken();
    $brand = Brand::where('api_key', $apiKey)->first();
    
    if (!$brand || !$brand->isActive()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    app()->instance('current_brand', $brand);
    app()->instance('current_tenant', $brand->tenant);
    
    return $next($request);
}
```

### Request Validation

```php
class StoreCallRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from' => ['required', 'string', 'regex:/^\+[1-9]\d{1,14}$/'],
            'to' => ['required', 'string', 'regex:/^\+[1-9]\d{1,14}$/'],
            'call_reason' => ['nullable', 'string', 'max:100'],
        ];
    }
}
```

---

## Frontend Development

### Inertia Pages

Pages are React components in `resources/js/Pages/`:

```tsx
// resources/js/Pages/Dashboard.tsx
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { PageProps } from '@/types';

interface DashboardProps extends PageProps {
    stats: { totalBrands: number; /* ... */ };
}

export default function Dashboard({ auth, stats }: DashboardProps) {
    return (
        <AuthenticatedLayout>
            <Head title="Dashboard" />
            {/* Component content */}
        </AuthenticatedLayout>
    );
}
```

### Passing Data from Controllers

```php
// DashboardController.php
public function index(Request $request)
{
    return Inertia::render('Dashboard', [
        'stats' => [
            'totalBrands' => Brand::count(),
            'activeBrands' => Brand::active()->count(),
        ],
        'recentBrands' => Brand::latest()->limit(5)->get(),
    ]);
}
```

### TypeScript Types

```typescript
// resources/js/types/index.d.ts
export interface Brand {
    id: number;
    name: string;
    slug: string;
    display_name: string | null;
    status: 'draft' | 'pending_vetting' | 'active' | 'suspended';
    logo_path: string | null;
}
```

---

## Testing

### Running Tests

```bash
# All tests
php artisan test

# Smoke tests only
composer smoke

# Specific test file
php artisan test --filter=SmokeTest

# With coverage
php artisan test --coverage
```

### Smoke Test Structure

```php
// tests/Feature/SmokeTest.php
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_dashboard_loads_for_owner(): void
    {
        $response = $this->actingAs($this->owner)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_tenants_loads(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/tenants');
        $response->assertStatus(200);
    }
}
```

### Test Users Setup

```php
protected function setUp(): void
{
    parent::setUp();
    
    $this->seed(RolesAndPermissionsSeeder::class);
    
    $this->tenant = Tenant::create([...]);
    
    $this->admin = User::create([...]);
    $this->admin->assignRole('super-admin');
    
    $this->owner = User::create(['tenant_id' => $this->tenant->id, ...]);
    $this->owner->assignRole('owner');
}
```

---

## Code Quality

### Linting (Laravel Pint)

```bash
# Fix code style
composer lint

# Check without fixing
composer lint:check
```

Configuration in `pint.json`:
- Laravel preset
- PHPDoc alignment
- Import ordering
- Blank lines before statements

### Static Analysis (Larastan)

```bash
# Run analysis
composer analyse
```

Configuration in `phpstan.neon`:
- Level 6 analysis
- Laravel-aware rules
- Custom ignores for edge cases

### All Quality Checks

```bash
composer quality  # lint:check + analyse
```

---

## Deployment

### Environment Variables

```env
# Application
APP_NAME=BrandCall
APP_ENV=production
APP_DEBUG=false

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=brandcall

# NumHub
NUMHUB_API_KEY=your_api_key
NUMHUB_API_SECRET=your_secret
NUMHUB_ENVIRONMENT=production

# Stripe
STRIPE_KEY=pk_live_xxx
STRIPE_SECRET=sk_live_xxx
```

### Build Commands

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev
npm ci

# Build frontend
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
php artisan filament:cache-components
```

### Database Migration

```bash
php artisan migrate --force
php artisan db:seed --class=PricingTierSeeder
```

---

## Common Tasks

### Creating a New Resource

```bash
# Filament resource with auto-generated form/table
php artisan make:filament-resource ModelName --generate
```

### Adding a Permission

```php
// In RolesAndPermissionsSeeder
Permission::create(['name' => 'new permission']);
$owner->givePermissionTo('new permission');
```

### Adding a Migration

```bash
php artisan make:migration add_column_to_table
php artisan migrate
```

### Generating API Keys

```php
// In Brand model
public function regenerateApiKey(): string
{
    $newKey = 'bci_' . bin2hex(random_bytes(32));
    $this->update(['api_key' => $newKey]);
    return $newKey;
}
```
