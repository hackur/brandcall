<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'KYC & Compliance';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) User::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return User::where('status', 'pending')->count() > 0 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified (KYC Submitted)',
                                'approved' => 'Approved',
                                'suspended' => 'Suspended',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Toggle::make('is_admin')
                            ->label('Admin Access')
                            ->helperText('Allow access to admin panel'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Company Information')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('company_website')
                            ->url()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('company_phone')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('industry')
                            ->maxLength(100),

                        Forms\Components\Select::make('monthly_call_volume')
                            ->options([
                                'under_1k' => 'Under 1,000',
                                '1k_10k' => '1,000 - 10,000',
                                '10k_100k' => '10,000 - 100,000',
                                '100k_1m' => '100,000 - 1M',
                                'over_1m' => 'Over 1M',
                            ])
                            ->native(false),

                        Forms\Components\Select::make('use_case')
                            ->options([
                                'sales' => 'Sales & Marketing',
                                'support' => 'Customer Support',
                                'notifications' => 'Notifications & Alerts',
                                'collections' => 'Collections',
                                'appointments' => 'Appointment Reminders',
                                'surveys' => 'Surveys & Research',
                                'other' => 'Other',
                            ])
                            ->native(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'verified' => 'info',
                        'approved' => 'success',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('documents_count')
                    ->label('Docs')
                    ->counts('documents')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'approved' => 'Approved',
                        'suspended' => 'Suspended',
                    ]),

                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Verified')
                    ->nullable(),

                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('Admin Users'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve User')
                    ->modalDescription('This will grant the user full access to the platform.')
                    ->visible(fn (User $record) => in_array($record->status, ['pending', 'verified']))
                    ->action(function (User $record) {
                        $record->update(['status' => 'approved']);

                        Notification::make()
                            ->title('User approved')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Suspend User')
                    ->modalDescription('This will revoke the user\'s access to the platform.')
                    ->visible(fn (User $record) => $record->status !== 'suspended')
                    ->action(function (User $record) {
                        $record->update(['status' => 'suspended']);

                        Notification::make()
                            ->title('User suspended')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['status' => 'approved']));

                            Notification::make()
                                ->title('Users approved')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('suspend')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['status' => 'suspended']));

                            Notification::make()
                                ->title('Users suspended')
                                ->warning()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentsRelationManager::class,
            RelationManagers\SupportTicketsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
