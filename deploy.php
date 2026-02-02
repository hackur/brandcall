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
]);

// Keep 5 releases for rollback
set('keep_releases', 5);

// Don't use sudo
set('writable_use_sudo', false);

// HTTP user
set('http_user', 'www-data');

// Hosts
host('production')
    ->set('hostname', '178.156.223.166')
    ->set('remote_user', 'root')
    ->set('deploy_path', '/var/www/brandcall')
    ->set('identity_file', '~/.ssh/id_rsa');

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

// Fix permissions
task('deploy:permissions', function () {
    run('chown -R www-data:www-data {{release_path}}');
    run('chmod -R 775 {{release_path}}/storage {{release_path}}/bootstrap/cache');
});

// Override deploy:update_code to use rsync instead of git
task('deploy:update_code', function () {
    // rsync task handles this
});

// Main deploy task using rsync
desc('Deploy BrandCall');
task('deploy', [
    'deploy:info',
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'rsync',                    // Upload code via rsync (replaces deploy:update_code)
    'deploy:shared',            // Symlink shared files
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
