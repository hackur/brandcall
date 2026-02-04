<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Z3d0X\FilamentFabricator\FilamentFabricatorPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            // No ->login() - use main app login, redirect based on role
            ->brandName('BrandCall Admin')
            ->colors([
                'primary' => Color::Purple,
                'danger' => Color::Red,
                'warning' => Color::Amber,
                'success' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Customer Management'),
                NavigationGroup::make()
                    ->label('Support'),
                NavigationGroup::make()
                    ->label('Developer Tools')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->collapsed(),
            ])
            ->navigationItems([
                // Developer Tools - only visible to super-admins
                NavigationItem::make('Horizon')
                    ->url('/horizon', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-queue-list')
                    ->group('Developer Tools')
                    ->sort(1)
                    ->visible(fn (): bool => auth()->user()?->hasRole('super-admin') ?? false),
                NavigationItem::make('Telescope')
                    ->url('/telescope', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-sparkles')
                    ->group('Developer Tools')
                    ->sort(2)
                    ->visible(fn (): bool => auth()->user()?->hasRole('super-admin') ?? false),
                NavigationItem::make('Pulse')
                    ->url('/pulse', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-chart-bar')
                    ->group('Developer Tools')
                    ->sort(3)
                    ->visible(fn (): bool => auth()->user()?->hasRole('super-admin') ?? false),
                NavigationItem::make('Health Check')
                    ->url('/health', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-heart')
                    ->group('Developer Tools')
                    ->sort(4)
                    ->visible(fn (): bool => auth()->user()?->hasRole('super-admin') ?? false),
                NavigationItem::make('API Docs')
                    ->url('/docs/api', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-document-text')
                    ->group('Developer Tools')
                    ->sort(5)
                    ->visible(fn (): bool => auth()->user()?->hasRole('super-admin') ?? false),
                NavigationItem::make('Log Viewer')
                    ->url('/log-viewer', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->group('Developer Tools')
                    ->sort(6)
                    ->visible(fn (): bool => auth()->user()?->hasRole('super-admin') ?? false),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->plugins([
                FilamentFabricatorPlugin::make(),
            ]);
    }
}
