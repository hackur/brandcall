<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LandingPageLeadResource\Pages;
use App\Models\LandingPageLead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LandingPageLeadResource extends Resource
{
    protected static ?string $model = LandingPageLead::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Leads';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contact Info')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->tel(),
                        Forms\Components\TextInput::make('company')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Message')
                    ->schema([
                        Forms\Components\Textarea::make('message')
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(LandingPageLead::STATUSES)
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->placeholder('Internal notes about this lead...'),
                    ])->columns(2),

                Forms\Components\Section::make('Tracking')
                    ->schema([
                        Forms\Components\TextInput::make('utm_source')
                            ->disabled(),
                        Forms\Components\TextInput::make('utm_medium')
                            ->disabled(),
                        Forms\Components\TextInput::make('utm_campaign')
                            ->disabled(),
                        Forms\Components\TextInput::make('referrer')
                            ->disabled(),
                    ])->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('landingPage.name')
                    ->label('Landing Page')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options(LandingPageLead::STATUSES),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('landing_page_id')
                    ->relationship('landingPage', 'name')
                    ->label('Landing Page'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(LandingPageLead::STATUSES),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLandingPageLeads::route('/'),
            'edit' => Pages\EditLandingPageLead::route('/{record}/edit'),
        ];
    }
}
