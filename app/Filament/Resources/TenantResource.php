<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Filament\Resources\TenantResource\RelationManagers;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Platform';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Used for API routing and subdomains'),
                    ])->columns(2),

                Forms\Components\Section::make('Subscription')
                    ->schema([
                        Forms\Components\Select::make('subscription_tier')
                            ->options([
                                'starter' => 'Starter',
                                'growth' => 'Growth',
                                'enterprise' => 'Enterprise',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('monthly_call_limit')
                            ->numeric()
                            ->nullable()
                            ->helperText('Leave empty for unlimited'),
                        Forms\Components\TextInput::make('stripe_customer_id')
                            ->label('Stripe Customer ID')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(3),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('analytics_monitoring_enabled')
                            ->label('Analytics Monitoring')
                            ->helperText('Enable advanced call analytics'),
                        Forms\Components\KeyValue::make('settings')
                            ->label('Custom Settings')
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('slug')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('subscription_tier')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'starter' => 'gray',
                        'growth' => 'info',
                        'enterprise' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('brands_count')
                    ->label('Brands')
                    ->counts('brands')
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subscription_tier')
                    ->options([
                        'starter' => 'Starter',
                        'growth' => 'Growth',
                        'enterprise' => 'Enterprise',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\UsersRelationManager::class,
            // RelationManagers\BrandsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'view' => Pages\ViewTenant::route('/{record}'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
