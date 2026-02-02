<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->label('Review Notes'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('')
                    ->width(50)
                    ->height(50)
                    ->defaultImageUrl(fn (Document $record) => $record->getMetadata()['is_pdf']
                        ? 'https://cdn-icons-png.flaticon.com/512/337/337946.png'
                        : null)
                    ->getStateUsing(fn (Document $record) => $record->getThumbnailUrl())
                    ->circular(false),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Document::getTypeLabel($state)),

                Tables\Columns\TextColumn::make('name')
                    ->limit(30),

                Tables\Columns\TextColumn::make('size')
                    ->getStateUsing(fn (Document $record) => $record->getMetadata()['size_formatted']),

                Tables\Columns\TextColumn::make('extension')
                    ->getStateUsing(fn (Document $record) => strtoupper($record->getMetadata()['extension']))
                    ->badge()
                    ->color('gray'),

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
                    ->dateTime('M j, Y'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (Document $record) => $record->name)
                    ->modalContent(fn (Document $record) => new HtmlString(
                        $record->getMetadata()['is_pdf']
                            ? '<iframe src="' . $record->getPreviewUrl() . '" class="w-full h-[60vh]"></iframe>'
                            : '<img src="' . ($record->getPreviewUrl() ?? $record->getDownloadUrl()) . '" class="max-w-full max-h-[60vh] mx-auto" />'
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->action(fn (Document $record) => $record->markAsViewed()),

                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Document $record) => $record->getDownloadUrl())
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Document $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Document $record) {
                        $record->update([
                            'status' => 'approved',
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                        ]);

                        Notification::make()->title('Document approved')->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Document $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Rejection reason')
                            ->required(),
                    ])
                    ->action(function (Document $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'notes' => $data['notes'],
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                        ]);

                        Notification::make()->title('Document rejected')->warning()->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('approve_all')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        $records->each(fn ($record) => $record->update([
                            'status' => 'approved',
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                        ]));

                        Notification::make()->title('Documents approved')->success()->send();
                    }),
            ]);
    }
}
