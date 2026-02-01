<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\VoiceProvider;
use App\Services\Voice\VoiceManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * Voice Service Provider.
 *
 * Registers the Voice manager and facade for branded calling.
 */
class VoiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/voice.php',
            'voice'
        );

        // Register the manager as a singleton
        $this->app->singleton('voice', function (Application $app) {
            return new VoiceManager($app);
        });

        // Bind the contract to the default driver
        $this->app->bind(VoiceProvider::class, function (Application $app) {
            return $app->make('voice')->driver();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../../config/voice.php' => config_path('voice.php'),
        ], 'voice-config');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return ['voice', VoiceProvider::class];
    }
}
