<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/rsync.php';

// Project name
set('application', 'brandcall');

// Use rsync instead of git (faster, works without server git access)
set('rsync_src', __DIR__);
set('rsync_dest', '{{release_path}}');
set('rsync', [
    'exclude' => [
        '.git',
        'node_modules',
        'vendor',
        '.env',
        'storage/logs/*',
        'storage/framework/cache/*',
        'storage/framework/sessions/*',
        'storage/framework/views/*',
        'bootstrap/cache/*',
        'tests',
        '.github',
        '.idea',
        '.vscode',
    ],
    'exclude-file' => false,
    'include' => [],
    'include-file' => false,
    'filter' => [],
    'filter-file' => false,
    'filter-perdir' => false,
    'flags' => 'avz',
    'options' => ['delete', 'delete-after', 'force'],
    'timeout' => 300,
]);

// Shared files/dirs between deploys
add('shared_files', [
    '.env',
]);

add('shared_dirs', [
    'storage',
]);

// Writable dirs by web server
add('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);

// Keep 5 releases for rollback
set('keep_releases', 5);

// Don't use sudo
set('writable_use_sudo', false);

// HTTP user
set('http_user', 'www-data');

// Writable mode - use chmod
set('writable_mode', 'chmod');
set('writable_chmod_mode', '0775');
set('writable_chmod_recursive', true);

// Hosts - supports env vars for CI/CD or falls back to defaults
host('production')
    ->set('hostname', getenv('DEPLOYER_HOST') ?: '178.156.223.166')
    ->set('remote_user', getenv('DEPLOYER_USER') ?: 'root')
    ->set('deploy_path', getenv('DEPLOYER_PATH') ?: '/var/www/brandcall')
    ->set('identity_file', getenv('DEPLOYER_KEY') ?: '~/.ssh/id_rsa');

// Build frontend assets locally before deploy
task('build', function () {
    runLocally('npm ci');
    runLocally('npm run build');
});

// Install composer dependencies (no dev)
task('deploy:vendors', function () {
    cd('{{release_path}}');
    run('COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader');
});

// Laravel-specific tasks
task('artisan:storage:link', function () {
    cd('{{release_path}}');
    run('{{bin/php}} artisan storage:link --force || true');
});

task('artisan:optimize', function () {
    cd('{{release_path}}');
    run('{{bin/php}} artisan optimize');
});

task('artisan:migrate', function () {
    cd('{{release_path}}');
    run('{{bin/php}} artisan migrate --force');
});

task('artisan:horizon:terminate', function () {
    cd('{{release_path}}');
    run('{{bin/php}} artisan horizon:terminate || true');
});

task('artisan:queue:restart', function () {
    cd('{{release_path}}');
    run('{{bin/php}} artisan queue:restart || true');
});

// Ensure shared storage directories exist with proper structure
task('deploy:storage:setup', function () {
    $sharedPath = '{{deploy_path}}/shared';

    // Create all required storage directories
    $dirs = [
        'storage/app',
        'storage/app/public',
        'storage/framework',
        'storage/framework/cache',
        'storage/framework/cache/data',
        'storage/framework/sessions',
        'storage/framework/testing',
        'storage/framework/views',
        'storage/logs',
    ];

    foreach ($dirs as $dir) {
        run("mkdir -p {$sharedPath}/{$dir}");
    }

    // Create .gitignore files to prevent git issues
    run("echo '*\n!.gitignore' > {$sharedPath}/storage/framework/cache/.gitignore || true");
    run("echo '*\n!.gitignore' > {$sharedPath}/storage/framework/sessions/.gitignore || true");
    run("echo '*\n!.gitignore' > {$sharedPath}/storage/framework/views/.gitignore || true");

    // Set ownership and permissions
    run("chown -R www-data:www-data {$sharedPath}/storage");
    run("chmod -R 775 {$sharedPath}/storage");
});

// Fix permissions on release
task('deploy:permissions', function () {
    run('chown -R www-data:www-data {{release_path}}');
    run('chmod -R 775 {{release_path}}/bootstrap/cache');
    // Storage is symlinked to shared, permissions handled by deploy:storage:setup
});

// Override deploy:update_code to use rsync instead of git
task('deploy:update_code', function () {
    // rsync task handles this
});

// Backup database before deploy
desc('Backup database');
task('backup:database', function () {
    $backupDir = '{{deploy_path}}/backups';
    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = "{$backupDir}/brandcall_{$timestamp}.sql.gz";

    // Create backup directory
    run("mkdir -p {$backupDir}");
    run("chmod 700 {$backupDir}");

    // Get DB credentials from .env
    $envFile = '{{deploy_path}}/shared/.env';
    
    writeln('<info>Creating database backup...</info>');
    
    // Run mysqldump with credentials from environment
    run("source {$envFile} && mysqldump --single-transaction --routines --triggers " .
        "-u\$DB_USERNAME -p\$DB_PASSWORD \$DB_DATABASE | gzip > {$backupFile}");
    
    // Show backup info
    $size = run("du -h {$backupFile} | cut -f1");
    writeln("<info>✅ Backup created: {$backupFile} ({$size})</info>");
    
    // Cleanup old backups (keep last 30 days)
    run("find {$backupDir} -name 'brandcall_*.sql.gz' -type f -mtime +30 -delete");
    
    // List recent backups
    writeln('<comment>Recent backups:</comment>');
    run("ls -lh {$backupDir}/brandcall_*.sql.gz 2>/dev/null | tail -5 || true");
});

// Main deploy task using rsync
desc('Deploy BrandCall');
task('deploy', [
    'deploy:info',
    'deploy:setup',
    'backup:database',          // ⬅️ Backup before deploy
    'deploy:lock',
    'deploy:release',
    'deploy:storage:setup',     // Ensure shared storage dirs exist
    'rsync',                    // Upload code via rsync (replaces deploy:update_code)
    'deploy:shared',            // Symlink shared files
    'deploy:writable',          // Set writable permissions
    'deploy:vendors',           // Install composer deps
    'artisan:storage:link',
    'artisan:migrate',
    'artisan:optimize',
    'deploy:permissions',
    'deploy:symlink',           // Atomic symlink switch
    'deploy:unlock',
    'artisan:horizon:terminate',
    'artisan:queue:restart',
    'deploy:cleanup',           // Remove old releases
    'deploy:success',
]);

// Hooks
after('deploy:failed', 'deploy:unlock');

// After rollback, optimize the current release
task('rollback:optimize', function () {
    cd('{{deploy_path}}/current');
    run('{{bin/php}} artisan optimize');
});

after('rollback', 'rollback:optimize');
