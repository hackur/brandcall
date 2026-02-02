# BrandCall Deployment Guide

## Overview

BrandCall uses **Deployer** for zero-downtime deployments with automatic rollback capability.

### Key Features
- ✅ Zero-downtime deployments via atomic symlink switching
- ✅ Automatic rollbacks on failure
- ✅ 5 release history for manual rollbacks
- ✅ Shared storage and .env across releases
- ✅ GitHub Actions CI/CD integration

---

## Quick Commands

```bash
# Deploy to production
composer deploy

# Rollback to previous release
composer rollback

# List all releases
composer releases

# SSH into production server
composer ssh

# Unlock after failed deploy
composer deploy:unlock
```

---

## Server Structure

```
/var/www/brandcall/
├── current -> releases/5      # Symlink to active release
├── releases/
│   ├── 1/                     # First release
│   ├── 2/                     # Second release
│   ├── 3/                     # Third release
│   ├── 4/                     # Fourth release
│   └── 5/                     # Latest release (active)
└── shared/
    ├── .env                   # Shared environment file
    └── storage/               # Shared storage directory
        ├── app/
        ├── framework/
        │   ├── cache/
        │   ├── sessions/
        │   └── views/
        └── logs/
```

---

## Deployment Flow

1. **Prepare** — Create new release directory
2. **Clone/Upload** — Get code into release
3. **Composer Install** — Install PHP dependencies (no dev)
4. **Symlink Shared** — Link .env and storage
5. **Build Assets** — Upload pre-built frontend assets
6. **Migrate** — Run database migrations
7. **Optimize** — Cache config, routes, views
8. **Permissions** — Fix storage permissions
9. **Activate** — Atomic symlink switch (zero downtime)
10. **Cleanup** — Restart queues, cleanup old releases

---

## CI/CD with GitHub Actions

### Automatic Deploys

Pushing to `main` triggers automatic deployment:

```
Push to main → CI Tests → Build Assets → Deploy
```

### Required Secrets

Add these to GitHub repository Settings → Secrets:

| Secret | Description |
|--------|-------------|
| `SSH_PRIVATE_KEY` | Private SSH key for server access |
| `DOT_ENV` | Production .env contents (optional) |

### Manual Deploy

```bash
# From local machine
npm run build
composer deploy
```

---

## Rollback

### Automatic Rollback

If deployment fails, Deployer automatically rolls back to the previous release.

### Manual Rollback

```bash
# Rollback to previous release
composer rollback

# Or via Deployer directly
vendor/bin/dep rollback production
```

### Rollback to Specific Release

```bash
# List releases first
composer releases

# SSH and manually switch
ssh root@178.156.223.166
cd /var/www/brandcall
ln -sfn /var/www/brandcall/releases/3 /var/www/brandcall/current
```

---

## Troubleshooting

### Deploy Locked

If a deploy fails and leaves a lock:

```bash
composer deploy:unlock
```

### Permission Errors

```bash
ssh root@178.156.223.166
chown -R www-data:www-data /var/www/brandcall
chmod -R 775 /var/www/brandcall/shared/storage
```

### Clear All Caches

```bash
ssh root@178.156.223.166
cd /var/www/brandcall/current
php artisan optimize:clear
php artisan optimize
```

### View Logs

```bash
ssh root@178.156.223.166
tail -f /var/www/brandcall/shared/storage/logs/laravel.log
```

---

## Configuration

### deploy.php

Main Deployer configuration. Key settings:

```php
// Number of releases to keep
set('keep_releases', 5);

// SSH user
->set('remote_user', 'root')

// Deploy path
->set('deploy_path', '/var/www/brandcall')
```

### Nginx

Points to `/var/www/brandcall/current/public`:

```nginx
root /var/www/brandcall/current/public;
```

---

## Server Requirements

- PHP 8.3+
- Composer 2.x
- Node.js 22+ (for local builds)
- Nginx
- MariaDB 10.11+
- Redis (for queues/cache)

---

## Health Checks

After deployment, verify:

1. **Homepage** — https://brandcall.io
2. **Admin** — https://brandcall.io/admin
3. **Health** — https://brandcall.io/health (if configured)

---

## Hotfixes

For urgent fixes that can't wait for full deploy:

```bash
# SSH to server
ssh root@178.156.223.166

# Edit file directly (not recommended)
cd /var/www/brandcall/current
nano app/SomeFile.php

# Clear caches
php artisan optimize:clear
php artisan optimize
```

**Note:** Direct edits will be lost on next deploy. Always commit changes to git.
