# BrandCall Deployment Guide

> CI/CD with GitHub Actions + Envoyer (or manual rsync)

---

## Quick Deploy (Current Setup)

Until Envoyer is configured, deploy manually:

```bash
# 1. Build frontend assets locally
npm run build

# 2. Rsync to server (excludes vendor, node_modules, .env)
rsync -avz --delete \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='vendor' \
  --exclude='.env' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='bootstrap/cache/*' \
  -e "ssh -i ~/.ssh/id_rsa" \
  . root@178.156.223.166:/var/www/brandcall/

# 3. Server-side: install dependencies + migrate + clear cache
ssh -i ~/.ssh/id_rsa root@178.156.223.166 "cd /var/www/brandcall && \
  composer install --no-interaction --optimize-autoloader --no-dev && \
  php artisan migrate --force && \
  php artisan config:clear && \
  php artisan cache:clear && \
  php artisan view:clear"
```

### One-Liner (All Steps)

```bash
cd /Volumes/JS-DEV/brandcall && \
npm run build && \
rsync -avz --delete \
  --exclude='.git' --exclude='node_modules' --exclude='vendor' --exclude='.env' \
  --exclude='storage/logs/*' --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' --exclude='storage/framework/views/*' \
  --exclude='bootstrap/cache/*' \
  -e "ssh -i ~/.ssh/id_rsa" \
  . root@178.156.223.166:/var/www/brandcall/ && \
ssh -i ~/.ssh/id_rsa root@178.156.223.166 "cd /var/www/brandcall && \
  composer install --no-interaction --optimize-autoloader --no-dev && \
  php artisan migrate --force && \
  php artisan config:clear && php artisan cache:clear && php artisan view:clear"
```

---

## Architecture Overview (Future: Envoyer)

```
┌─────────────────────────────────────────────────────────────────────┐
│                         DEPLOYMENT FLOW                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│   Developer        GitHub          GitHub Actions      Envoyer      │
│                                                                     │
│   ┌──────┐       ┌────────┐       ┌────────────┐    ┌──────────┐   │
│   │ Push │──────▶│  main  │──────▶│   CI Jobs  │───▶│  Deploy  │   │
│   └──────┘       └────────┘       └────────────┘    └────┬─────┘   │
│                                         │                 │         │
│                                    On Success        Webhook        │
│                                         │                 │         │
│                                    ┌────▼────┐      ┌────▼─────┐   │
│                                    │  Lint   │      │  Server  │   │
│                                    │  Test   │      │ Release  │   │
│                                    │ Analyse │      │ Rollback │   │
│                                    │ Audit   │      └──────────┘   │
│                                    └─────────┘                      │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## GitHub Actions CI Pipeline

### Jobs Run on Every Push/PR

| Job | Purpose | Failure = Block Deploy |
|-----|---------|------------------------|
| **lint** | Laravel Pint code style | Yes |
| **analyse** | PHPStan static analysis | Yes |
| **security** | Composer audit for CVEs | Yes |
| **test** | PHPUnit/Pest tests (PHP 8.2, 8.3) | Yes |
| **build** | Verify production build works | Yes |

### Trigger Deployment

Deployment only triggers when:
1. Push to `main` branch
2. All CI jobs pass
3. Envoyer webhook is called

---

## Envoyer Setup

### Step 1: Create Envoyer Project

1. Go to [envoyer.io](https://envoyer.io)
2. Create new project → "BrandCall"
3. Connect to GitHub repository

### Step 2: Configure Server

Add the production server:
- **Name:** brandcall-production
- **IP:** 178.156.223.166
- **User:** www-data (or deploy user)
- **PHP Path:** /usr/bin/php8.3

### Step 3: Configure Project Settings

**Repository:**
- Branch: `main`
- Repository: `your-org/brandcall`

**Project Path:**
```
/var/www/brandcall
```

**Releases to Retain:** 5

### Step 4: Deployment Hooks

Add these hooks in Envoyer:

#### Before: Install Composer Dependencies
```bash
cd {{release}}
composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader
```

#### Before: Install NPM & Build Assets
```bash
cd {{release}}
npm ci --no-audit
npm run build
```

#### After: Clear Caches
```bash
cd {{release}}
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### After: Cache Configuration
```bash
cd {{release}}
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

#### After: Run Migrations
```bash
cd {{release}}
php artisan migrate --force
```

#### After: Restart Queue Workers
```bash
cd {{release}}
php artisan queue:restart
```

#### After: Restart Horizon (if installed)
```bash
cd {{release}}
php artisan horizon:terminate || true
```

### Step 5: Shared Directories/Files

Configure in Envoyer:

**Shared Directories:**
- `storage/app`
- `storage/framework/cache`
- `storage/framework/sessions`
- `storage/framework/views`
- `storage/logs`

**Shared Files:**
- `.env`

### Step 6: Get Deploy Hook URL

1. In Envoyer project settings → Deployment Hooks
2. Copy the webhook URL
3. Add to GitHub repository secrets as `ENVOYER_DEPLOY_HOOK`

### Step 7: GitHub Secrets

Add to GitHub → Settings → Secrets → Actions:

| Secret | Value |
|--------|-------|
| `ENVOYER_DEPLOY_HOOK` | Your Envoyer webhook URL |

---

## Server Directory Structure (After Envoyer)

```
/var/www/brandcall/
├── current -> releases/20260201143000    # Symlink to active release
├── releases/
│   ├── 20260201143000/                   # Latest release
│   ├── 20260201120000/                   # Previous release
│   ├── 20260131180000/                   # Older release
│   └── ...
├── shared/
│   ├── .env                              # Shared environment file
│   └── storage/                          # Shared storage directory
│       ├── app/
│       ├── framework/
│       └── logs/
└── .envoyer/                             # Envoyer metadata
```

---

## Manual Operations

### Rollback

In Envoyer dashboard:
1. Go to Deployments
2. Click "Rollback" on any previous deployment

Or via command line on server:
```bash
cd /var/www/brandcall
ln -sfn releases/PREVIOUS_RELEASE current
sudo systemctl reload php8.3-fpm
```

### Force Deploy

Trigger deployment without code changes:
1. GitHub → Actions → Deploy → Run workflow
2. Or push an empty commit: `git commit --allow-empty -m "deploy"`

### Health Check

```bash
# On server
cd /var/www/brandcall/current
php artisan about
php artisan route:list --compact
php artisan config:show app.env
```

---

## Nginx Configuration Update

Update Nginx to use the `current` symlink:

```nginx
server {
    server_name brandcall.io www.brandcall.io;
    
    # Point to current release
    root /var/www/brandcall/current/public;
    
    # ... rest of config
}
```

After updating:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

---

## Local Development Commands

```bash
# Run tests
composer test

# Check code style
composer lint        # Auto-fix
composer lint:check  # Check only

# Static analysis
composer analyse

# All quality checks (lint + analyse)
composer quality

# Smoke test
composer smoke
```

---

## Troubleshooting

### Deployment Failed

1. Check Envoyer deployment log
2. SSH to server and check:
   ```bash
   tail -f /var/www/brandcall/current/storage/logs/laravel.log
   ```

### CI Failed

1. Check GitHub Actions logs
2. Run locally:
   ```bash
   composer quality
   composer test
   ```

### Rollback Needed

1. Use Envoyer dashboard "Rollback"
2. Or SSH and manually symlink previous release

---

## Environment Variables

### Required for Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://brandcall.io

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=brandcall
DB_USERNAME=brandcall
DB_PASSWORD=xxx

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
```

### Envoyer-Managed

The `.env` file is in `/var/www/brandcall/shared/.env` and symlinked to each release.

Edit via:
1. Envoyer dashboard → Environment
2. Or SSH: `nano /var/www/brandcall/shared/.env`

---

## Security Notes

- Deploy key is read-only (GitHub → Envoyer)
- Server SSH uses key-based auth only
- `.env` file is not in Git
- Secrets stored in GitHub Secrets + Envoyer Environment

---

## Server Information

| Property | Value |
|----------|-------|
| **IP** | 178.156.223.166 |
| **Hostname** | brandcall-web |
| **Provider** | Hetzner Cloud (CPX21) |
| **Location** | Ashburn, VA (us-east) |
| **OS** | Ubuntu 24.04 LTS |
| **PHP** | 8.4 |
| **Database** | MariaDB 10.11 |
| **Web Server** | Nginx 1.24 |
| **Node.js** | 22 |
| **Web Root** | `/var/www/brandcall/public` |
| **SSH** | `ssh -i ~/.ssh/id_rsa root@178.156.223.166` |

### Database Credentials

```
DB_DATABASE=brandcall
DB_USERNAME=brandcall
DB_PASSWORD=BrandCall2026Secure!
```

---

*Last updated: February 2026*
