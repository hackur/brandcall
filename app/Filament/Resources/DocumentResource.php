<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationGroup = 'KYC & Compliance';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) Document::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return Document::where('status', 'pending')->count() > 0 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Information')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending Review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Textarea::make('notes')
                            ->label('Review Notes')
                            ->placeholder('Add notes for the user (visible to them)...')
                            ->rows(3),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Document::getTypeLabel($state))
                    ->color(fn (string $state): string => match ($state) {
                        'business_license', 'tax_id', 'articles_incorporation' => 'info',
                        'drivers_license', 'government_id' => 'warning',
                        'kyc' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('original_filename')
                    ->label('File')
                    ->limit(25)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Reviewed')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                Tables\Filters\SelectFilter::make('type')
                    ->options(fn () => collect(Document::getAllTypes())
                        ->pluck('label', 'value')
                        ->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn (Document $record) => route('filament.admin.documents.download', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Document')
                    ->modalDescription('Are you sure you want to approve this document?')
                    ->visible(fn (Document $record) => $record->status === 'pending')
                    ->action(function (Document $record) {
                        $record->update([
                            'status' => 'approved',
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Document approved')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Document')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Reason for rejection')
                            ->required()
                            ->placeholder('Explain why this document was rejected...')
                            ->rows(3),
                    ])
                    ->visible(fn (Document $record) => $record->status === 'pending')
                    ->action(function (Document $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'notes' => $data['notes'],
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Document rejected')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update([
                                'status' => 'approved',
                                'reviewed_at' => now(),
                                'reviewed_by' => auth()->id(),
                            ]));

                            Notification::make()
                                ->title('Documents approved')
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
                Infolists\Components\Section::make('Document Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('type')
                            ->formatStateUsing(fn (string $state): string => Document::getTypeLabel($state)),
                        Infolists\Components\TextEntry::make('original_filename')
                            ->label('Original Filename'),
                        Infolists\Components\TextEntry::make('mime_type')
                            ->label('File Type'),
                        Infolists\Components\TextEntry::make('size')
                            ->formatStateUsing(fn (int $state): string => number_format($state / 1024, 1) . ' KB'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('User Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Name'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('user.company_name')
                            ->label('Company'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Review')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Review Notes')
                            ->placeholder('No notes'),
                        Infolists\Components\TextEntry::make('reviewed_at')
                            ->label('Reviewed At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('reviewer.name')
                            ->label('Reviewed By'),
                    ])
                    ->columns(3)
                    ->visible(fn (Document $record) => $record->reviewed_at !== null),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
            'view' => Pages\ViewDocument::route('/{record}'),
        ];
    }
}
