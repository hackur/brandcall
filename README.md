# BrandCall

**Branded Caller ID SaaS Platform** - Display your company name, logo, and call reason on outbound calls.

Built with Laravel 12, Inertia.js, React, TypeScript, and Filament.

## Features

- **Branded Calling**: Display business name, logo, and call reason
- **STIR/SHAKEN Compliant**: FCC-mandated call authentication
- **Multi-tenant Architecture**: Row-level security for customer isolation
- **Rich Call Data (RCD)**: Enhanced caller ID on supported devices
- **Volume-based Pricing**: Automatic tier discounts ($0.075 → $0.025)
- **Analytics Dashboard**: Call metrics and campaign tracking (coming soon)

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | React 19, TypeScript, Tailwind CSS v4 |
| Admin Panel | Filament 3 |
| SPA Bridge | Inertia.js |
| Auth | Laravel Sanctum, Spatie Permission |
| Database | SQLite (dev), MySQL/PostgreSQL (prod) |
| API | Voice Provider (pluggable) |

## Requirements

- PHP 8.2+
- Node.js 20+
- Composer 2.x
- SQLite / MySQL / PostgreSQL

## Installation

```bash
# Clone the repository
git clone https://github.com/your-org/brandcall.git
cd brandcall

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Start development servers
composer dev
```

## Development

### Running the Application

```bash
# Start all dev servers (Laravel, Vite, Queue, Logs)
composer dev

# Or run individually:
php artisan serve --port=8001    # Backend
npm run dev                       # Vite frontend
php artisan queue:listen          # Queue worker
php artisan pail                  # Log viewer
```

### Code Quality

```bash
# Run linter (Laravel Pint)
composer lint

# Check linting without fixing
composer lint:check

# Run static analysis (PHPStan/Larastan)
composer analyse

# Run all quality checks
composer quality

# Run tests
composer test
```

### Building for Production

```bash
npm run build
php artisan optimize
```

## Architecture

### Multi-tenancy

BrandCall uses row-level security via `TenantScope`:

```php
// Automatically applied to tenant-scoped models
class Brand extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }
}
```

### Roles & Permissions

Using Spatie Laravel Permission:

| Role | Description | Permissions |
|------|-------------|-------------|
| `super-admin` | Platform administrator | All permissions |
| `owner` | Tenant owner | Full tenant access |
| `admin` | Tenant administrator | Most tenant access |
| `member` | Team member | Read + make calls |

### API Structure

```
POST /api/v1/brands/{slug}/calls     # Initiate branded call
GET  /api/v1/brands/{slug}/calls     # List call history
GET  /api/v1/brands/{slug}/analytics # Get brand analytics
```

## Project Structure

```
app/
├── Filament/           # Admin panel resources
├── Http/
│   ├── Controllers/    # Web controllers
│   └── Middleware/     # Request middleware
├── Models/             # Eloquent models
├── Scopes/             # Query scopes (TenantScope)
└── Services/           # Business logic (Voice providers, etc.)

resources/
├── js/
│   ├── Components/     # React components
│   ├── Layouts/        # Page layouts
│   └── Pages/          # Inertia pages
└── views/              # Blade templates

docs/
├── FEATURES.md         # Feature comparison & roadmap
└── API.md              # API documentation
```

## Configuration

### Voice Provider

Set in `.env`:

```env
VOICE_DRIVER=numhub  # or telnyx, twilio
VOICE_API_KEY=your_api_key
VOICE_API_SECRET=your_api_secret
```

### Stripe Billing

```env
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
```

## Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter BrandTest
```

## Documentation

- [Feature Roadmap](docs/FEATURES.md)
- [API Reference](docs/API.md)
- [Contributing Guide](CONTRIBUTING.md)

## License

Proprietary - All rights reserved.

## Credits

Built by Jeremy Sarda.
