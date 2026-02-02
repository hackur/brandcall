# Laravel Ecosystem Packages

> Comprehensive tooling for development, monitoring, and operations

---

## Packages Installed

### Development Tools

| Package | Purpose | Access |
|---------|---------|--------|
| **Laravel Telescope** | Request/query debugging, exception tracking | `/telescope` (local only) |
| **Laravel IDE Helper** | PHPStorm/VSCode autocomplete | CLI only |
| **Pest** | Modern testing framework | CLI only |
| **Larastan** | PHPStan + Laravel rules | CLI only |
| **Laravel Pint** | Code style fixer (PSR-12 + Laravel) | CLI only |

### Production Tools

| Package | Purpose | Access |
|---------|---------|--------|
| **Laravel Horizon** | Queue monitoring dashboard | `/horizon` |
| **Laravel Pulse** | Real-time application monitoring | `/pulse` |
| **Spatie Health** | Health checks endpoint | `/health` |
| **Spatie Activity Log** | Model change auditing | Code/DB |
| **Spatie Backup** | Database & file backups | CLI/Scheduler |
| **Spatie Permissions** | Role/permission management | Code/Filament |

---

## Development Tools

### Laravel Telescope

Debug your application with detailed insights into:
- Requests & responses
- Database queries (with slow query highlighting)
- Exceptions
- Logs
- Jobs & queues
- Mail
- Notifications
- Cache operations
- Scheduled tasks

**Access:** `/telescope` (only in `local` environment)

**Configuration:** `config/telescope.php`

```bash
# Prune old entries
php artisan telescope:prune

# Clear all entries
php artisan telescope:clear
```

### Laravel IDE Helper

Generate helper files for better IDE autocomplete:

```bash
# Generate model docblocks
php artisan ide-helper:models --write

# Generate facade helper
php artisan ide-helper:generate

# Generate meta file for PhpStorm
php artisan ide-helper:meta
```

Add to `.gitignore`:
```
_ide_helper.php
_ide_helper_models.php
.phpstorm.meta.php
```

### Pest (Testing)

Modern PHP testing framework with elegant syntax:

```php
// tests/Feature/ExampleTest.php
it('has a welcome page', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

test('users can login', function () {
    $user = User::factory()->create();
    
    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect('/dashboard');
});
```

**Run tests:**
```bash
# All tests
php artisan test

# With coverage
php artisan test --coverage

# Specific file
php artisan test tests/Feature/BrandTest.php

# Parallel
php artisan test --parallel
```

### Larastan (Static Analysis)

PHPStan with Laravel-specific rules:

```bash
# Run analysis
./vendor/bin/phpstan analyse

# With higher level
./vendor/bin/phpstan analyse --level=8

# Generate baseline (ignore existing errors)
./vendor/bin/phpstan analyse --generate-baseline
```

**Configuration:** `phpstan.neon`

### Laravel Pint (Code Style)

Laravel's opinionated PHP code style fixer:

```bash
# Fix all files
./vendor/bin/pint

# Check only (no changes)
./vendor/bin/pint --test

# Specific directory
./vendor/bin/pint app/Models

# Show diff
./vendor/bin/pint -v
```

**Configuration:** `pint.json`

---

## Production Tools

### Laravel Horizon

Redis queue dashboard and management:

**Access:** `/horizon`

**Features:**
- Real-time queue metrics
- Job throughput graphs
- Failed job management
- Process supervision

**Commands:**
```bash
# Start Horizon
php artisan horizon

# Pause processing
php artisan horizon:pause

# Continue processing  
php artisan horizon:continue

# Graceful termination
php artisan horizon:terminate

# Check status
php artisan horizon:status
```

**Supervisor Config:** `/etc/supervisor/conf.d/horizon.conf`
```ini
[program:horizon]
process_name=%(program_name)s
command=php /var/www/brandcall/current/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/brandcall/shared/storage/logs/horizon.log
stopwaitsecs=3600
```

### Laravel Pulse

Real-time application performance monitoring:

**Access:** `/pulse`

**Metrics tracked:**
- Slow requests
- Slow queries
- Slow jobs
- Exceptions
- Memory usage
- CPU usage
- User activity

**Configuration:** `config/pulse.php`

```bash
# Prune old data
php artisan pulse:prune

# Check status
php artisan pulse:check
```

### Spatie Health

Application health monitoring:

**Access:** 
- `/health` - HTML dashboard
- `/health/json` - JSON API (for monitoring tools)

**Checks configured:**
- ✅ Environment is production
- ✅ Debug mode is off
- ✅ App is optimized
- ✅ Database connection
- ✅ Redis connection
- ✅ Cache working
- ✅ Queue processing
- ✅ Horizon running
- ✅ Scheduler running
- ✅ Disk space

**Schedule check:**
```php
// app/Console/Kernel.php
$schedule->command('health:check')->everyMinute();
```

### Spatie Activity Log

Automatic model change tracking:

```php
// In your model
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Brand extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'display_name', 'is_active'])
            ->logOnlyDirty();
    }
}
```

**Query logs:**
```php
// Get all activity
Activity::all();

// For a model
$brand->activities;

// Filter by event
Activity::where('event', 'updated')->get();
```

### Spatie Backup

Automated database and file backups:

**Configuration:** `config/backup.php`

**Commands:**
```bash
# Run backup
php artisan backup:run

# Database only
php artisan backup:run --only-db

# List backups
php artisan backup:list

# Monitor backup health
php artisan backup:monitor

# Clean old backups
php artisan backup:clean
```

**Schedule:**
```php
// app/Console/Kernel.php
$schedule->command('backup:clean')->daily()->at('01:00');
$schedule->command('backup:run')->daily()->at('02:00');
```

---

## CLI Commands Reference

### Quality Assurance

```bash
# All quality checks (lint + analyse)
composer quality

# Code style only
composer lint        # Fix issues
composer lint:check  # Check only

# Static analysis
composer analyse

# Tests
composer test
composer smoke       # Smoke tests only
```

### Development

```bash
# Start development server (all services)
composer dev

# Generate IDE helpers
php artisan ide-helper:models --write
php artisan ide-helper:generate
```

### Production

```bash
# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Clear caches
php artisan optimize:clear

# Queue management
php artisan horizon
php artisan queue:work
php artisan queue:restart

# Health check
php artisan health:check
```

---

## Dashboard URLs

| URL | Purpose | Access |
|-----|---------|--------|
| `/admin` | Filament Admin Panel | Super-admin only |
| `/horizon` | Queue Dashboard | Admin email only |
| `/pulse` | Performance Monitoring | Admin email only |
| `/telescope` | Debug Dashboard | Local env only |
| `/health` | Health Checks | Authenticated users |

---

## Environment Variables

```env
# Horizon
HORIZON_ENABLED=true

# Telescope (auto-disabled in production)
TELESCOPE_ENABLED=true

# Pulse
PULSE_ENABLED=true

# Backup
BACKUP_DISK=local
BACKUP_NOTIFICATION_EMAIL=admin@brandcall.com

# Health notifications
HEALTH_NOTIFICATION_EMAIL=admin@brandcall.com
```

---

*Last updated: February 2026*
