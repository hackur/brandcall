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
        // Only register Telescope in local environment
        if ($this->app->environment('local')) {
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

        // Horizon authorization
        Horizon::auth(function ($request) {
            if ($this->app->environment('local')) {
                return true;
            }

            return $request->user()?->email === 'admin@brandcall.io';
        });

        // Pulse authorization
        Gate::define('viewPulse', function ($user) {
            return $user->email === 'admin@brandcall.io';
        });

        // Pulse user resolution
        Pulse::user(fn ($user) => [
            'name' => $user->name,
            'extra' => $user->email,
            'avatar' => 'https://www.gravatar.com/avatar/' . md5(strtolower($user->email)),
        ]);
    }
}
