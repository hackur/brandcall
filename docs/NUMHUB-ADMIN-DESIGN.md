# NumHub Filament Admin Panel Design

> **Design Document for NumHub BrandControl Integration Admin Interface**
> 
> Date: 2026-02-04
> Status: Draft

---

## Table of Contents

1. [Overview](#overview)
2. [Navigation Structure](#navigation-structure)
3. [Settings Page](#1-settings-page)
4. [Application Dashboard](#2-application-dashboard)
5. [Resources](#3-resources)
6. [Widgets](#4-widgets)
7. [Actions](#5-actions)
8. [Implementation Order](#implementation-order)

---

## Overview

This document defines the Filament admin interface for managing NumHub BrandControl integration. The design follows existing BrandCall conventions established in `UserResource`, `DocumentResource`, and `TenantResource`.

### Design Principles

- **Consistent with existing patterns** - Matches BrandCall Filament resource structure
- **Action-oriented** - Quick actions prominently displayed
- **Status visibility** - Clear indicators for sync state, token health, rate limits
- **Audit trail** - Full API request/response logging for debugging

### Prerequisites (Database Models)

These models must be created before implementing the admin interface:

```php
// Models from TODO-NUMHUB-INTEGRATION.md
App\Models\NumHubToken      // Token storage with expiry
App\Models\NumHubEntity     // Business → NumhubEntityId mapping
App\Models\NumHubIdentity   // Phone → NumhubIdentityId mapping  
App\Models\NumHubSyncLog    // API audit trail
App\Models\NumHubDocument   // Document tracking for LOA uploads
```

---

## Navigation Structure

```
Admin Panel
├── Dashboard (default)
├── KYC & Compliance
│   ├── Documents (existing)
│   └── Users (existing)
├── NumHub Integration (NEW GROUP)
│   ├── NumHub Dashboard (NumHubDashboard.php)
│   ├── Entity Mappings (NumHubEntityResource.php)
│   ├── API Sync Log (NumHubSyncLogResource.php)
│   ├── Settlement Reports (SettlementReportResource.php)
│   └── Settings (NumHubSettingsPage.php)
├── Platform
│   └── Tenants (existing)
└── Developer Tools (existing)
```

---

## 1. Settings Page

> **File:** `app/Filament/Pages/NumHubSettingsPage.php`

### Wireframe

```
┌──────────────────────────────────────────────────────────────────┐
│ NumHub Settings                                                   │
├──────────────────────────────────────────────────────────────────┤
│                                                                   │
│ ┌─ API Credentials ──────────────────────────────────────────┐   │
│ │                                                             │   │
│ │ API URL         [https://brandidentity-api.numhub.com    ] │   │
│ │ Email           [••••••••••••••••••••••                  ] │   │
│ │ Password        [••••••••••••••••••••••                  ] │   │
│ │ Client ID       [••••••••••••••••••••••                  ] │   │
│ │                                                             │   │
│ │                              [Test Connection] [Save]       │   │
│ └─────────────────────────────────────────────────────────────┘   │
│                                                                   │
│ ┌─ Token Status ─────────────────────────────────────────────┐   │
│ │                                                             │   │
│ │   ● Connected                Token expires in 23h 45m       │   │
│ │                                                             │   │
│ │   Last refreshed: Feb 4, 2026 2:15 PM                       │   │
│ │   Token ID: tok_abc123...                                   │   │
│ │                                                             │   │
│ │                                       [Refresh Token Now]   │   │
│ └─────────────────────────────────────────────────────────────┘   │
│                                                                   │
│ ┌─ Rate Limit Monitor ───────────────────────────────────────┐   │
│ │                                                             │   │
│ │   Requests this minute: 23 / 100                            │   │
│ │   ████████░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░  23%              │   │
│ │                                                             │   │
│ │   Requests today: 1,245                                     │   │
│ │   429 errors (last 24h): 0                                  │   │
│ │                                                             │   │
│ └─────────────────────────────────────────────────────────────┘   │
│                                                                   │
│ ┌─ Sync Configuration ───────────────────────────────────────┐   │
│ │                                                             │   │
│ │   [x] Auto-sync new businesses on approval                  │   │
│ │   [x] Auto-refresh token before expiry                      │   │
│ │   [ ] Sync webhook notifications                            │   │
│ │                                                             │   │
│ │   Retry attempts on failure: [3        ]                    │   │
│ │   Backoff delay (seconds):   [30       ]                    │   │
│ │                                                             │   │
│ └─────────────────────────────────────────────────────────────┘   │
│                                                                   │
└──────────────────────────────────────────────────────────────────┘
```

### Code Structure

```php
<?php

namespace App\Filament\Pages;

use App\Services\NumHub\NumHubClient;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Crypt;

class NumHubSettingsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationGroup = 'NumHub Integration';
    
    protected static ?int $navigationSort = 99;
    
    protected static ?string $title = 'NumHub Settings';
    
    protected static string $view = 'filament.pages.numhub-settings';

    public ?array $data = [];
    
    // Token status data (loaded separately)
    public ?array $tokenStatus = null;
    public ?array $rateLimitStatus = null;

    public function mount(): void
    {
        $this->form->fill([
            'api_url' => config('numhub.api_url'),
            'email' => config('numhub.email'),
            'password' => '', // Never pre-fill password
            'client_id' => config('numhub.client_id'),
            'auto_sync_on_approval' => config('numhub.auto_sync_on_approval', true),
            'auto_refresh_token' => config('numhub.auto_refresh_token', true),
            'webhook_notifications' => config('numhub.webhook_notifications', false),
            'retry_attempts' => config('numhub.retry_attempts', 3),
            'backoff_delay' => config('numhub.backoff_delay', 30),
        ]);
        
        $this->loadTokenStatus();
        $this->loadRateLimitStatus();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('API Credentials')
                    ->description('Configure NumHub BrandControl API connection')
                    ->schema([
                        Forms\Components\TextInput::make('api_url')
                            ->label('API URL')
                            ->url()
                            ->required()
                            ->default('https://brandidentity-api.numhub.com'),
                        
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->autocomplete('off'),
                        
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->helperText('Leave blank to keep existing password'),
                        
                        Forms\Components\TextInput::make('client_id')
                            ->label('Client ID')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Sync Configuration')
                    ->schema([
                        Forms\Components\Toggle::make('auto_sync_on_approval')
                            ->label('Auto-sync new businesses on approval')
                            ->helperText('Automatically submit to NumHub when a business is approved'),
                        
                        Forms\Components\Toggle::make('auto_refresh_token')
                            ->label('Auto-refresh token before expiry')
                            ->helperText('Automatically refresh token when less than 1 hour remaining'),
                        
                        Forms\Components\Toggle::make('webhook_notifications')
                            ->label('Sync webhook notifications')
                            ->helperText('Pull NumHub alerts and notifications'),
                        
                        Forms\Components\TextInput::make('retry_attempts')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->default(3),
                        
                        Forms\Components\TextInput::make('backoff_delay')
                            ->label('Backoff delay (seconds)')
                            ->numeric()
                            ->minValue(5)
                            ->maxValue(300)
                            ->default(30),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testConnection')
                ->label('Test Connection')
                ->icon('heroicon-o-signal')
                ->color('info')
                ->action(function () {
                    try {
                        $client = app(NumHubClient::class);
                        $userInfo = $client->getUserInfo();
                        
                        Notification::make()
                            ->title('Connection successful')
                            ->body("Connected as: {$userInfo['email']}")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Connection failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            
            Action::make('refreshToken')
                ->label('Refresh Token Now')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        $client = app(NumHubClient::class);
                        $client->refreshToken(force: true);
                        
                        $this->loadTokenStatus();
                        
                        Notification::make()
                            ->title('Token refreshed')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Token refresh failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // Update .env or database settings
        // Note: In production, use Spatie Settings or similar
        $this->updateSettings($data);
        
        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    protected function loadTokenStatus(): void
    {
        $token = \App\Models\NumHubToken::latest()->first();
        
        if ($token && $token->expires_at > now()) {
            $this->tokenStatus = [
                'connected' => true,
                'expires_at' => $token->expires_at,
                'expires_in' => $token->expires_at->diffForHumans(),
                'last_refreshed' => $token->updated_at->format('M j, Y g:i A'),
                'token_preview' => 'tok_' . substr($token->access_token, 0, 8) . '...',
            ];
        } else {
            $this->tokenStatus = [
                'connected' => false,
                'message' => $token ? 'Token expired' : 'No token found',
            ];
        }
    }

    protected function loadRateLimitStatus(): void
    {
        $this->rateLimitStatus = [
            'requests_this_minute' => cache()->get('numhub:rate_limit:count', 0),
            'limit' => 100,
            'requests_today' => \App\Models\NumHubSyncLog::whereDate('created_at', today())->count(),
            'errors_24h' => \App\Models\NumHubSyncLog::where('created_at', '>=', now()->subDay())
                ->where('status_code', 429)
                ->count(),
        ];
    }

    protected function updateSettings(array $data): void
    {
        // Implementation depends on settings storage strategy
        // Option 1: Spatie Laravel Settings package
        // Option 2: Database settings table
        // Option 3: .env update (not recommended for production)
    }
}
```

### Blade View

```blade
{{-- resources/views/filament/pages/numhub-settings.blade.php --}}

<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                Save Settings
            </x-filament::button>
        </div>
    </form>

    {{-- Token Status Card --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Token Status</x-slot>
        
        @if($tokenStatus['connected'] ?? false)
            <div class="flex items-center gap-2">
                <x-heroicon-s-check-circle class="w-5 h-5 text-success-500" />
                <span class="font-medium text-success-600">Connected</span>
                <span class="text-gray-500">— Token expires {{ $tokenStatus['expires_in'] }}</span>
            </div>
            <div class="mt-2 text-sm text-gray-500">
                <p>Last refreshed: {{ $tokenStatus['last_refreshed'] }}</p>
                <p>Token ID: {{ $tokenStatus['token_preview'] }}</p>
            </div>
        @else
            <div class="flex items-center gap-2">
                <x-heroicon-s-x-circle class="w-5 h-5 text-danger-500" />
                <span class="font-medium text-danger-600">{{ $tokenStatus['message'] ?? 'Not connected' }}</span>
            </div>
        @endif
    </x-filament::section>

    {{-- Rate Limit Monitor --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Rate Limit Monitor</x-slot>
        
        <div class="space-y-3">
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span>Requests this minute</span>
                    <span>{{ $rateLimitStatus['requests_this_minute'] }} / {{ $rateLimitStatus['limit'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary-500 h-2 rounded-full" 
                         style="width: {{ ($rateLimitStatus['requests_this_minute'] / $rateLimitStatus['limit']) * 100 }}%">
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Requests today:</span>
                    <span class="font-medium">{{ number_format($rateLimitStatus['requests_today']) }}</span>
                </div>
                <div>
                    <span class="text-gray-500">429 errors (24h):</span>
                    <span class="font-medium {{ $rateLimitStatus['errors_24h'] > 0 ? 'text-danger-600' : '' }}">
                        {{ $rateLimitStatus['errors_24h'] }}
                    </span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>
```

---

## 2. Application Dashboard

> **File:** `app/Filament/Pages/NumHubDashboard.php`

### Wireframe

```
┌──────────────────────────────────────────────────────────────────┐
│ NumHub Dashboard                           [Sync All] [Refresh]   │
├──────────────────────────────────────────────────────────────────┤
│                                                                   │
│ ┌────────────┐ ┌────────────┐ ┌────────────┐ ┌────────────┐      │
│ │     23     │ │     12     │ │     45     │ │      3     │      │
│ │  Pending   │ │ Processing │ │  Approved  │ │  Rejected  │      │
│ │ ○ warning  │ │ ○ info     │ │ ○ success  │ │ ○ danger   │      │
│ └────────────┘ └────────────┘ └────────────┘ └────────────┘      │
│                                                                   │
│ ┌─ Application Status Chart ──────────────────────────────────┐  │
│ │                                                              │  │
│ │   45 ████████████████████████████████████████  Approved      │  │
│ │   23 ██████████████████████                    Pending       │  │
│ │   12 ██████████                                Processing    │  │
│ │    3 ██                                        Rejected      │  │
│ │                                                              │  │
│ └──────────────────────────────────────────────────────────────┘  │
│                                                                   │
│ ┌─ Recent Applications ──────────────────────────────────────┐   │
│ │                                                             │   │
│ │ Company                  Status      NumHub ID    Actions   │   │
│ │ ─────────────────────────────────────────────────────────── │   │
│ │ Acme Healthcare Inc     ● Approved   NH-12345    [View]     │   │
│ │ Pacific Insurance       ● Pending    —           [Sync]     │   │
│ │ Metro Dental Group      ● Processing NH-12346    [View]     │   │
│ │ Golden Financial        ● Rejected   NH-12340    [Retry]    │   │
│ │ ...                                                         │   │
│ │                                              [View All →]   │   │
│ └─────────────────────────────────────────────────────────────┘   │
│                                                                   │
│ ┌─ Recent Alerts ─────────────────┐ ┌─ Sync Status ───────────┐  │
│ │                                  │ │                         │  │
│ │ ⚠ Number flagged: +1555...      │ │ Last sync: 5 min ago    │  │
│ │   Feb 4, 2:30 PM                │ │ Status: ● Healthy       │  │
│ │                                  │ │                         │  │
│ │ ⚠ LOA expiring: Acme Corp       │ │ Pending sync: 3         │  │
│ │   Feb 4, 1:15 PM                │ │ Failed (24h): 0         │  │
│ │                                  │ │                         │  │
│ │ ℹ Entity updated: Metro...      │ │ ─────────────────────── │  │
│ │   Feb 4, 12:00 PM               │ │ Queue: 0 jobs waiting   │  │
│ │                                  │ │                         │  │
│ └──────────────────────────────────┘ └─────────────────────────┘  │
│                                                                   │
└──────────────────────────────────────────────────────────────────┘
```

### Code Structure

```php
<?php

namespace App\Filament\Pages;

use App\Filament\Widgets;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class NumHubDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    
    protected static ?string $navigationGroup = 'NumHub Integration';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $title = 'NumHub Dashboard';
    
    protected static string $view = 'filament.pages.numhub-dashboard';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncAll')
                ->label('Sync All Pending')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Sync All Pending Entities')
                ->modalDescription('This will submit all pending businesses to NumHub. This may take several minutes.')
                ->action(function () {
                    // Dispatch batch sync job
                    \App\Jobs\NumHub\SyncAllPendingEntities::dispatch();
                    
                    Notification::make()
                        ->title('Sync started')
                        ->body('Syncing all pending entities in the background.')
                        ->success()
                        ->send();
                }),
            
            Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->dispatch('$refresh')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Widgets\NumHubStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            Widgets\ApplicationStatusChart::class,
            Widgets\RecentApplicationsWidget::class,
            Widgets\RecentAlertsWidget::class,
            Widgets\SyncStatusWidget::class,
        ];
    }

    public function getWidgetData(): array
    {
        return [];
    }
}
```

---

## 3. Resources

### 3.1 NumHubEntityResource

> **File:** `app/Filament/Resources/NumHubEntityResource.php`

Manages the mapping between BrandCall businesses/brands and NumHub entities.

### Wireframe

```
┌──────────────────────────────────────────────────────────────────┐
│ NumHub Entity Mappings                        [Sync Selected]     │
├──────────────────────────────────────────────────────────────────┤
│ [Search...                    ] Status [All      ▼] Synced [▼]   │
├──────────────────────────────────────────────────────────────────┤
│ □ | Business        | NumHub ID | Status     | Last Sync | Act.  │
│───┼─────────────────┼───────────┼────────────┼───────────┼───────│
│ □ | Acme Healthcare | NH-12345  | ● Approved | 2h ago    | ⋮     │
│ □ | Pacific Ins.    | —         | ● Pending  | Never     | ⋮     │
│ □ | Metro Dental    | NH-12346  | ● In Review| 1d ago    | ⋮     │
│ □ | Golden Fin.     | NH-12340  | ● Rejected | 3d ago    | ⋮     │
└──────────────────────────────────────────────────────────────────┘
```

### Code Structure

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NumHubEntityResource\Pages;
use App\Jobs\NumHub\SyncEntityToNumHub;
use App\Models\NumHubEntity;
use App\Services\NumHub\NumHubClient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NumHubEntityResource extends Resource
{
    protected static ?string $model = NumHubEntity::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'NumHub Integration';

    protected static ?int $navigationSort = 2;
    
    protected static ?string $recordTitleAttribute = 'business_name';
    
    protected static ?string $modelLabel = 'Entity Mapping';
    
    protected static ?string $pluralModelLabel = 'Entity Mappings';

    public static function getNavigationBadge(): ?string
    {
        return (string) NumHubEntity::whereNull('numhub_entity_id')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return NumHubEntity::whereNull('numhub_entity_id')->count() > 0 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Local Reference')
                    ->schema([
                        Forms\Components\Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('NumHub Reference')
                    ->schema([
                        Forms\Components\TextInput::make('numhub_entity_id')
                            ->label('NumHub Entity ID')
                            ->disabled()
                            ->placeholder('Assigned after sync'),
                        
                        Forms\Components\Select::make('numhub_status')
                            ->label('NumHub Status')
                            ->options([
                                'pending' => 'Pending (Not Synced)',
                                'submitted' => 'Submitted',
                                'in_review' => 'In Review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'suspended' => 'Suspended',
                            ])
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('attestation_level')
                            ->label('Attestation Level')
                            ->disabled()
                            ->placeholder('A, B, or C'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Sync History')
                    ->schema([
                        Forms\Components\DateTimePicker::make('last_synced_at')
                            ->disabled(),
                        
                        Forms\Components\DateTimePicker::make('numhub_updated_at')
                            ->label('Last NumHub Update')
                            ->disabled(),
                        
                        Forms\Components\Textarea::make('last_error')
                            ->label('Last Error')
                            ->disabled()
                            ->rows(2),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Business')
                    ->description(fn (NumHubEntity $record) => $record->tenant?->name)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('numhub_entity_id')
                    ->label('NumHub ID')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->copyable(),

                Tables\Columns\TextColumn::make('numhub_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'submitted' => 'info',
                        'in_review' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('attestation_level')
                    ->label('Attestation')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'A' => 'success',
                        'B' => 'warning',
                        'C' => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('last_synced_at')
                    ->label('Last Sync')
                    ->since()
                    ->sortable()
                    ->placeholder('Never'),

                Tables\Columns\IconColumn::make('has_error')
                    ->label('Error')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->getStateUsing(fn (NumHubEntity $record) => !empty($record->last_error)),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('numhub_status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'submitted' => 'Submitted',
                        'in_review' => 'In Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                Tables\Filters\TernaryFilter::make('synced')
                    ->label('Synced to NumHub')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('numhub_entity_id'),
                        false: fn ($query) => $query->whereNull('numhub_entity_id'),
                    ),

                Tables\Filters\TernaryFilter::make('has_error')
                    ->label('Has Error')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('last_error'),
                        false: fn ($query) => $query->whereNull('last_error'),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('sync')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Sync to NumHub')
                    ->modalDescription('This will submit/update this entity in NumHub.')
                    ->action(function (NumHubEntity $record) {
                        SyncEntityToNumHub::dispatch($record);
                        
                        Notification::make()
                            ->title('Sync queued')
                            ->body('Entity will be synced in the background.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('viewNumHub')
                    ->label('View in NumHub')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (NumHubEntity $record) => $record->numhub_entity_id
                        ? "https://brandcontrol.numhub.com/entity/{$record->numhub_entity_id}"
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn (NumHubEntity $record) => !empty($record->numhub_entity_id)),

                Tables\Actions\Action::make('triggerOtp')
                    ->label('Send OTP')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('info')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\TextInput::make('phone_number')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->helperText('Phone number to send OTP for verification'),
                    ])
                    ->action(function (NumHubEntity $record, array $data) {
                        try {
                            $client = app(NumHubClient::class);
                            $client->generateOtp($record->numhub_entity_id, $data['phone_number']);
                            
                            Notification::make()
                                ->title('OTP sent')
                                ->body("Verification code sent to {$data['phone_number']}")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to send OTP')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (NumHubEntity $record) => !empty($record->numhub_entity_id)),

                Tables\Actions\Action::make('downloadLoa')
                    ->label('Download LOA')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function (NumHubEntity $record) {
                        try {
                            $client = app(NumHubClient::class);
                            return $client->downloadLoaTemplate($record->numhub_entity_id);
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to download LOA')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (NumHubEntity $record) => !empty($record->numhub_entity_id)),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('syncSelected')
                        ->label('Sync Selected')
                        ->icon('heroicon-o-arrow-path')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                SyncEntityToNumHub::dispatch($record);
                            }
                            
                            Notification::make()
                                ->title('Sync queued')
                                ->body(count($records) . ' entities queued for sync.')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('retryFailed')
                        ->label('Retry Failed')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $failed = $records->filter(fn ($r) => !empty($r->last_error));
                            foreach ($failed as $record) {
                                $record->update(['last_error' => null]);
                                SyncEntityToNumHub::dispatch($record);
                            }
                            
                            Notification::make()
                                ->title('Retry queued')
                                ->body($failed->count() . ' failed entities queued for retry.')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Business Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('brand.name')
                            ->label('Business Name'),
                        Infolists\Components\TextEntry::make('tenant.name')
                            ->label('Tenant'),
                        Infolists\Components\TextEntry::make('brand.phone')
                            ->label('Primary Phone'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('NumHub Status')
                    ->schema([
                        Infolists\Components\TextEntry::make('numhub_entity_id')
                            ->label('Entity ID')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('numhub_status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'warning',
                            }),
                        Infolists\Components\TextEntry::make('attestation_level')
                            ->badge(),
                        Infolists\Components\TextEntry::make('last_synced_at')
                            ->label('Last Sync')
                            ->dateTime(),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make('Error Log')
                    ->schema([
                        Infolists\Components\TextEntry::make('last_error')
                            ->label('')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (NumHubEntity $record) => !empty($record->last_error))
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\SyncLogsRelationManager::class,
            // RelationManagers\IdentitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNumHubEntities::route('/'),
            'view' => Pages\ViewNumHubEntity::route('/{record}'),
            'edit' => Pages\EditNumHubEntity::route('/{record}/edit'),
        ];
    }
}
```

---

### 3.2 NumHubSyncLogResource

> **File:** `app/Filament/Resources/NumHubSyncLogResource.php`

View-only audit trail of all NumHub API interactions.

### Wireframe

```
┌──────────────────────────────────────────────────────────────────┐
│ NumHub API Sync Log                                               │
├──────────────────────────────────────────────────────────────────┤
│ [Search...          ] Endpoint [All ▼] Status [All ▼] [Today ▼] │
├──────────────────────────────────────────────────────────────────┤
│ Time         | Endpoint              | Status | Duration | Act.  │
│──────────────┼───────────────────────┼────────┼──────────┼───────│
│ 2:45:23 PM   | POST /application     | 200 ✓  | 234ms    | [👁]  │
│ 2:44:12 PM   | GET /application/123  | 200 ✓  | 89ms     | [👁]  │
│ 2:43:01 PM   | POST /authorize/token | 200 ✓  | 156ms    | [👁]  │
│ 2:40:55 PM   | PUT /application/120  | 400 ✗  | 312ms    | [👁]  │
└──────────────────────────────────────────────────────────────────┘
```

### Code Structure

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NumHubSyncLogResource\Pages;
use App\Models\NumHubSyncLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NumHubSyncLogResource extends Resource
{
    protected static ?string $model = NumHubSyncLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'NumHub Integration';

    protected static ?int $navigationSort = 3;
    
    protected static ?string $modelLabel = 'API Log';
    
    protected static ?string $pluralModelLabel = 'API Logs';

    public static function canCreate(): bool
    {
        return false; // Read-only resource
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('g:i:s A')
                    ->description(fn (NumHubSyncLog $record) => $record->created_at->format('M j, Y'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'GET' => 'info',
                        'POST' => 'success',
                        'PUT' => 'warning',
                        'DELETE' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('endpoint')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn (NumHubSyncLog $record) => $record->endpoint),

                Tables\Columns\TextColumn::make('status_code')
                    ->label('Status')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 400 && $state < 500 => 'warning',
                        $state >= 500 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => $state . ' ' . ($state < 400 ? '✓' : '✗')),

                Tables\Columns\TextColumn::make('duration_ms')
                    ->label('Duration')
                    ->formatStateUsing(fn (int $state): string => $state . 'ms')
                    ->color(fn (int $state): string => match (true) {
                        $state < 200 => 'success',
                        $state < 500 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('numhub_entity_id')
                    ->label('Entity')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('System')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('method')
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'DELETE' => 'DELETE',
                    ]),

                Tables\Filters\SelectFilter::make('status_group')
                    ->label('Status')
                    ->options([
                        'success' => 'Success (2xx)',
                        'client_error' => 'Client Error (4xx)',
                        'server_error' => 'Server Error (5xx)',
                    ])
                    ->query(fn ($query, $state) => match ($state['value']) {
                        'success' => $query->whereBetween('status_code', [200, 299]),
                        'client_error' => $query->whereBetween('status_code', [400, 499]),
                        'server_error' => $query->whereBetween('status_code', [500, 599]),
                        default => $query,
                    }),

                Tables\Filters\Filter::make('today')
                    ->label('Today Only')
                    ->query(fn ($query) => $query->whereDate('created_at', today()))
                    ->toggle(),

                Tables\Filters\Filter::make('slow_requests')
                    ->label('Slow (>500ms)')
                    ->query(fn ($query) => $query->where('duration_ms', '>', 500))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Details'),
            ])
            ->poll('10s'); // Auto-refresh every 10 seconds
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Request')
                    ->schema([
                        Infolists\Components\TextEntry::make('method')
                            ->badge(),
                        Infolists\Components\TextEntry::make('endpoint')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Timestamp')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('duration_ms')
                            ->label('Duration')
                            ->formatStateUsing(fn (int $state): string => $state . 'ms'),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make('Request Body')
                    ->schema([
                        Infolists\Components\TextEntry::make('request_body')
                            ->label('')
                            ->formatStateUsing(fn (?string $state): string => 
                                $state ? json_encode(json_decode($state), JSON_PRETTY_PRINT) : '(empty)'
                            )
                            ->prose()
                            ->copyable(),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Response')
                    ->schema([
                        Infolists\Components\TextEntry::make('status_code')
                            ->label('Status Code')
                            ->badge()
                            ->color(fn (int $state): string => $state < 400 ? 'success' : 'danger'),
                        Infolists\Components\TextEntry::make('response_body')
                            ->label('Body')
                            ->formatStateUsing(fn (?string $state): string => 
                                $state ? json_encode(json_decode($state), JSON_PRETTY_PRINT) : '(empty)'
                            )
                            ->prose()
                            ->copyable(),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Context')
                    ->schema([
                        Infolists\Components\TextEntry::make('numhub_entity_id')
                            ->label('Entity ID'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Triggered By')
                            ->placeholder('System'),
                        Infolists\Components\TextEntry::make('ip_address')
                            ->label('IP Address'),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNumHubSyncLogs::route('/'),
            'view' => Pages\ViewNumHubSyncLog::route('/{record}'),
        ];
    }
}
```

---

### 3.3 SettlementReportResource

> **File:** `app/Filament/Resources/SettlementReportResource.php`

View NumHub billing and usage reports.

### Wireframe

```
┌──────────────────────────────────────────────────────────────────┐
│ Settlement Reports                              [Download CSV]    │
├──────────────────────────────────────────────────────────────────┤
│ Period [January 2026 ▼]     Tenant [All ▼]                       │
├──────────────────────────────────────────────────────────────────┤
│ Period     | Tenant           | Branded Calls | Cost    | Status │
│────────────┼──────────────────┼───────────────┼─────────┼────────│
│ Jan 2026   | Acme Healthcare  | 12,450        | $124.50 | Paid   │
│ Jan 2026   | Pacific Insurance| 8,200         | $82.00  | Pending│
│ Dec 2025   | Acme Healthcare  | 10,320        | $103.20 | Paid   │
└──────────────────────────────────────────────────────────────────┘
```

### Code Structure

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettlementReportResource\Pages;
use App\Models\SettlementReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettlementReportResource extends Resource
{
    protected static ?string $model = SettlementReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'NumHub Integration';

    protected static ?int $navigationSort = 4;
    
    protected static ?string $modelLabel = 'Settlement Report';

    public static function canCreate(): bool
    {
        return false; // Reports are pulled from NumHub
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('period')
                    ->label('Period')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('M Y'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_calls')
                    ->label('Branded Calls')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('successful_calls')
                    ->label('Delivered')
                    ->numeric()
                    ->description(fn ($record) => 
                        round(($record->successful_calls / max($record->total_calls, 1)) * 100, 1) . '% success'
                    )
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Cost')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'overdue' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('synced_at')
                    ->label('Last Sync')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('period', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('tenant')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'paid' => 'Paid',
                        'pending' => 'Pending',
                        'overdue' => 'Overdue',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(fn (SettlementReport $record) => 
                        response()->download($record->generateCsvPath())
                    ),
            ])
            ->headerActions([
                Tables\Actions\Action::make('syncReports')
                    ->label('Sync from NumHub')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->action(function () {
                        \App\Jobs\NumHub\SyncSettlementReports::dispatch();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Sync started')
                            ->body('Settlement reports are being synced.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('exportSelected')
                    ->label('Export Selected')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn ($records) => 
                        response()->download(
                            SettlementReport::generateBulkCsv($records)
                        )
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettlementReports::route('/'),
            'view' => Pages\ViewSettlementReport::route('/{record}'),
        ];
    }
}
```

---

## 4. Widgets

### 4.1 NumHubStatsOverview

> **File:** `app/Filament/Widgets/NumHubStatsOverview.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Models\NumHubEntity;
use App\Models\NumHubSyncLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NumHubStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Pending', NumHubEntity::where('numhub_status', 'pending')->count())
                ->description('Awaiting sync to NumHub')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart($this->getChartData('pending')),

            Stat::make('In Review', NumHubEntity::where('numhub_status', 'in_review')->count())
                ->description('Being processed by NumHub')
                ->descriptionIcon('heroicon-m-magnifying-glass')
                ->color('info'),

            Stat::make('Approved', NumHubEntity::where('numhub_status', 'approved')->count())
                ->description('Active branded entities')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart($this->getChartData('approved')),

            Stat::make('Rejected', NumHubEntity::where('numhub_status', 'rejected')->count())
                ->description('Require attention')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }

    protected function getChartData(string $status): array
    {
        // Last 7 days trend
        return NumHubEntity::where('numhub_status', $status)
            ->where('updated_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(updated_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }
}
```

---

### 4.2 ApplicationStatusChart

> **File:** `app/Filament/Widgets/ApplicationStatusChart.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Models\NumHubEntity;
use Filament\Widgets\ChartWidget;

class ApplicationStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Application Status Distribution';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = NumHubEntity::selectRaw('numhub_status, COUNT(*) as count')
            ->groupBy('numhub_status')
            ->pluck('count', 'numhub_status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Applications',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#f59e0b', // pending - amber
                        '#3b82f6', // submitted - blue
                        '#6366f1', // in_review - indigo
                        '#22c55e', // approved - green
                        '#ef4444', // rejected - red
                    ],
                ],
            ],
            'labels' => array_map(fn ($s) => ucfirst(str_replace('_', ' ', $s)), array_keys($data)),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
```

---

### 4.3 RecentApplicationsWidget

> **File:** `app/Filament/Widgets/RecentApplicationsWidget.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\NumHubEntityResource;
use App\Models\NumHubEntity;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentApplicationsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Applications';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                NumHubEntity::query()
                    ->with(['brand', 'tenant'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Company')
                    ->description(fn ($record) => $record->tenant?->name)
                    ->searchable(),

                Tables\Columns\TextColumn::make('numhub_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'submitted', 'in_review' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('numhub_entity_id')
                    ->label('NumHub ID')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (NumHubEntity $record): string => 
                        NumHubEntityResource::getUrl('view', ['record' => $record])
                    )
                    ->icon('heroicon-o-eye'),

                Tables\Actions\Action::make('sync')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->visible(fn (NumHubEntity $record) => $record->numhub_status === 'pending')
                    ->action(fn (NumHubEntity $record) => 
                        \App\Jobs\NumHub\SyncEntityToNumHub::dispatch($record)
                    ),
            ])
            ->paginated(false);
    }
}
```

---

### 4.4 RecentAlertsWidget

> **File:** `app/Filament/Widgets/RecentAlertsWidget.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Models\NumHubAlert;
use Filament\Widgets\Widget;

class RecentAlertsWidget extends Widget
{
    protected static string $view = 'filament.widgets.recent-alerts';
    
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 1;

    public function getAlerts(): \Illuminate\Support\Collection
    {
        return NumHubAlert::with('entity')
            ->where('dismissed_at', null)
            ->latest()
            ->limit(5)
            ->get();
    }

    public function dismissAlert(int $alertId): void
    {
        NumHubAlert::where('id', $alertId)->update(['dismissed_at' => now()]);
        $this->dispatch('$refresh');
    }
}
```

**Blade View:** `resources/views/filament/widgets/recent-alerts.blade.php`

```blade
<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Recent Alerts</x-slot>
        
        <div class="space-y-3">
            @forelse($this->getAlerts() as $alert)
                <div class="flex items-start gap-3 p-3 rounded-lg {{ $alert->severity === 'warning' ? 'bg-warning-50' : 'bg-danger-50' }}">
                    @if($alert->severity === 'warning')
                        <x-heroicon-s-exclamation-triangle class="w-5 h-5 text-warning-500 flex-shrink-0" />
                    @else
                        <x-heroicon-s-x-circle class="w-5 h-5 text-danger-500 flex-shrink-0" />
                    @endif
                    
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $alert->title }}</p>
                        <p class="text-xs text-gray-500">{{ $alert->created_at->diffForHumans() }}</p>
                    </div>
                    
                    <button 
                        wire:click="dismissAlert({{ $alert->id }})"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <x-heroicon-s-x-mark class="w-4 h-4" />
                    </button>
                </div>
            @empty
                <div class="text-center py-4 text-gray-500">
                    <x-heroicon-o-bell-slash class="w-8 h-8 mx-auto mb-2" />
                    <p class="text-sm">No alerts</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
```

---

### 4.5 SyncStatusWidget

> **File:** `app/Filament/Widgets/SyncStatusWidget.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Models\NumHubEntity;
use App\Models\NumHubSyncLog;
use App\Models\NumHubToken;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Queue;

class SyncStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.sync-status';
    
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 1;

    public function getSyncStatus(): array
    {
        $lastSync = NumHubSyncLog::latest()->first();
        $token = NumHubToken::latest()->first();
        
        $isHealthy = $token 
            && $token->expires_at > now() 
            && NumHubSyncLog::where('created_at', '>=', now()->subHour())
                ->where('status_code', '>=', 500)
                ->count() === 0;

        return [
            'last_sync' => $lastSync?->created_at?->diffForHumans() ?? 'Never',
            'is_healthy' => $isHealthy,
            'pending_sync' => NumHubEntity::where('numhub_status', 'pending')->count(),
            'failed_24h' => NumHubSyncLog::where('created_at', '>=', now()->subDay())
                ->where('status_code', '>=', 400)
                ->count(),
            'queue_size' => Queue::size('numhub'),
            'token_status' => $token && $token->expires_at > now() 
                ? 'Valid (' . $token->expires_at->diffForHumans() . ')' 
                : 'Expired/Missing',
        ];
    }
}
```

**Blade View:** `resources/views/filament/widgets/sync-status.blade.php`

```blade
@php $status = $this->getSyncStatus(); @endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Sync Status</x-slot>
        
        <div class="space-y-4">
            {{-- Health Indicator --}}
            <div class="flex items-center gap-2">
                @if($status['is_healthy'])
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-success-500"></span>
                    </span>
                    <span class="text-sm font-medium text-success-600">Healthy</span>
                @else
                    <span class="relative flex h-3 w-3">
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-danger-500"></span>
                    </span>
                    <span class="text-sm font-medium text-danger-600">Issues Detected</span>
                @endif
            </div>

            {{-- Stats Grid --}}
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-gray-500">Last sync</dt>
                    <dd class="font-medium">{{ $status['last_sync'] }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Pending sync</dt>
                    <dd class="font-medium">{{ $status['pending_sync'] }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Failed (24h)</dt>
                    <dd class="font-medium {{ $status['failed_24h'] > 0 ? 'text-danger-600' : '' }}">
                        {{ $status['failed_24h'] }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Queue</dt>
                    <dd class="font-medium">{{ $status['queue_size'] }} jobs</dd>
                </div>
            </dl>

            <hr class="border-gray-200">

            <div class="text-xs text-gray-500">
                Token: {{ $status['token_status'] }}
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
```

---

## 5. Actions

### Summary of All Actions

| Action | Location | Type | Description |
|--------|----------|------|-------------|
| **Test Connection** | Settings Page | Header | Tests NumHub API connectivity |
| **Refresh Token** | Settings Page | Header | Force-refresh access token |
| **Sync All Pending** | Dashboard | Header | Batch sync all pending entities |
| **Sync (single)** | Entity Resource | Row | Sync one entity to NumHub |
| **Bulk Sync** | Entity Resource | Bulk | Sync multiple selected entities |
| **View in NumHub** | Entity Resource | Row | Open NumHub portal for entity |
| **Trigger OTP** | Entity Resource | Row | Send OTP verification |
| **Download LOA** | Entity Resource | Row | Download LOA template |
| **Retry Failed** | Entity Resource | Bulk | Retry all failed syncs |
| **Sync Reports** | Settlement Resource | Header | Pull latest settlement reports |
| **Export CSV** | Settlement Resource | Row/Bulk | Download report as CSV |

### Action Code Patterns

All actions follow this pattern (from existing BrandCall resources):

```php
Tables\Actions\Action::make('actionName')
    ->label('Action Label')
    ->icon('heroicon-o-icon-name')
    ->color('primary|success|warning|danger|gray|info')
    ->requiresConfirmation() // Optional
    ->modalHeading('Confirm Action')
    ->modalDescription('Are you sure?')
    ->form([/* Optional form fields */])
    ->visible(fn ($record) => /* condition */)
    ->action(function ($record, ?array $data = null) {
        // Action logic
        
        Notification::make()
            ->title('Success')
            ->success()
            ->send();
    });
```

---

## Implementation Order

### Phase 1: Foundation (Before Admin UI)
1. Create database migrations for NumHub models
2. Create Eloquent models (`NumHubToken`, `NumHubEntity`, `NumHubSyncLog`, etc.)
3. Create `NumHubClient` service class
4. Create config/numhub.php

### Phase 2: Settings & Dashboard
1. `NumHubSettingsPage` - settings management
2. `NumHubDashboard` - overview page
3. `NumHubStatsOverview` widget
4. `SyncStatusWidget`

### Phase 3: Core Resources
1. `NumHubEntityResource` - entity management
2. `NumHubSyncLogResource` - API audit trail
3. Blade views for widgets

### Phase 4: Advanced Features
1. `SettlementReportResource` - billing reports
2. `ApplicationStatusChart` widget
3. `RecentApplicationsWidget`
4. `RecentAlertsWidget`
5. Background jobs for sync operations

### Phase 5: Polish
1. Navigation badges for pending items
2. Real-time polling on dashboard
3. Notification integration
4. Error handling improvements

---

## Files to Create

```
app/Filament/
├── Pages/
│   ├── NumHubSettingsPage.php
│   └── NumHubDashboard.php
├── Resources/
│   ├── NumHubEntityResource.php
│   ├── NumHubEntityResource/
│   │   └── Pages/
│   │       ├── ListNumHubEntities.php
│   │       ├── ViewNumHubEntity.php
│   │       └── EditNumHubEntity.php
│   ├── NumHubSyncLogResource.php
│   ├── NumHubSyncLogResource/
│   │   └── Pages/
│   │       ├── ListNumHubSyncLogs.php
│   │       └── ViewNumHubSyncLog.php
│   ├── SettlementReportResource.php
│   └── SettlementReportResource/
│       └── Pages/
│           ├── ListSettlementReports.php
│           └── ViewSettlementReport.php
└── Widgets/
    ├── NumHubStatsOverview.php
    ├── ApplicationStatusChart.php
    ├── RecentApplicationsWidget.php
    ├── RecentAlertsWidget.php
    └── SyncStatusWidget.php

resources/views/filament/
├── pages/
│   ├── numhub-settings.blade.php
│   └── numhub-dashboard.blade.php
└── widgets/
    ├── recent-alerts.blade.php
    └── sync-status.blade.php
```

---

*Last updated: 2026-02-04*
