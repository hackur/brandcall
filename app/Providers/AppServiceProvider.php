<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;
use Laravel\Pulse\Facades\Pulse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Telescope in local and production (with proper auth gates)
        if ($this->app->environment('local', 'production')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Define the super-admin check used across all dev dashboards
        $isSuperAdmin = fn ($user) => $user?->hasRole('super-admin') ?? false;

        // Horizon authorization - requires super-admin role
        Horizon::auth(function ($request) use ($isSuperAdmin) {
            if ($this->app->environment('local')) {
                return true;
            }

            return $isSuperAdmin($request->user());
        });

        // Pulse authorization - requires super-admin role
        Gate::define('viewPulse', fn ($user) => $isSuperAdmin($user));

        // Health Check authorization - requires super-admin role
        Gate::define('viewHealth', fn ($user) => $isSuperAdmin($user));

        // API Documentation (Scramble) - requires super-admin role
        Gate::define('viewApiDocs', fn ($user) => $isSuperAdmin($user));

        // Log Viewer authorization - requires super-admin role
        Gate::define('viewLogViewer', fn ($user) => $isSuperAdmin($user));

        // Pulse user resolution
        Pulse::user(fn ($user) => [
            'name' => $user->name,
            'extra' => $user->email,
            'avatar' => 'https://www.gravatar.com/avatar/' . md5(strtolower($user->email)),
        ]);
    }
}
