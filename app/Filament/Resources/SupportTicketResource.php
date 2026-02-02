<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupportTicketResource\Pages;
use App\Models\SupportTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Support';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'subject';

    public static function getNavigationBadge(): ?string
    {
        return (string) SupportTicket::whereIn('status', ['open', 'in_progress'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return SupportTicket::where('priority', 'urgent')->where('status', 'open')->exists()
            ? 'danger'
            : 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Information')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'open' => 'Open',
                                'in_progress' => 'In Progress',
                                'waiting' => 'Waiting on Customer',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('category')
                            ->options([
                                'general' => 'General Inquiry',
                                'billing' => 'Billing & Payments',
                                'technical' => 'Technical Support',
                                'kyc' => 'KYC / Verification',
                                'feature' => 'Feature Request',
                            ])
                            ->native(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->rows(5),
                    ]),
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

                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'warning',
                        'high' => 'danger',
                        'urgent' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'warning',
                        'in_progress' => 'info',
                        'waiting' => 'gray',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('replies_count')
                    ->label('Replies')
                    ->counts('replies')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Update')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'waiting' => 'Waiting',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'general' => 'General',
                        'billing' => 'Billing',
                        'technical' => 'Technical',
                        'kyc' => 'KYC',
                        'feature' => 'Feature',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('reply')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('primary')
                    ->form([
                        Forms\Components\Textarea::make('message')
                            ->label('Reply')
                            ->required()
                            ->rows(5),
                    ])
                    ->action(function (SupportTicket $record, array $data) {
                        $record->replies()->create([
                            'user_id' => auth()->id(),
                            'message' => $data['message'],
                        ]);

                        if ($record->status === 'open') {
                            $record->update(['status' => 'in_progress']);
                        }

                        Notification::make()
                            ->title('Reply sent')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (SupportTicket $record) => ! in_array($record->status, ['resolved', 'closed']))
                    ->action(function (SupportTicket $record) {
                        $record->update(['status' => 'resolved']);

                        Notification::make()
                            ->title('Ticket resolved')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('close')
                        ->icon('heroicon-o-x-mark')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['status' => 'closed']));

                            Notification::make()
                                ->title('Tickets closed')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupportTickets::route('/'),
            'view' => Pages\ViewSupportTicket::route('/{record}'),
            'edit' => Pages\EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
