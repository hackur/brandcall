<?php

declare(strict_types=1);

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

/**
 * Features grid block for displaying product features.
 *
 * Supports configurable columns and icon selection.
 */
class FeaturesBlock extends PageBlock
{
    /**
     * Get the block schema for the Filament form builder.
     */
    public static function getBlockSchema(): Block
    {
        return Block::make('features')
            ->label('Features Grid')
            ->icon('heroicon-o-squares-2x2')
            ->schema([
                TextInput::make('eyebrow')
                    ->label('Eyebrow Text')
                    ->placeholder('e.g., Features'),

                TextInput::make('heading')
                    ->label('Section Heading')
                    ->placeholder('e.g., Everything You Need'),

                Textarea::make('description')
                    ->label('Section Description')
                    ->rows(2),

                Repeater::make('features')
                    ->label('Features')
                    ->schema([
                        Select::make('icon')
                            ->label('Icon')
                            ->options([
                                'shield-check' => 'Shield Check',
                                'paint-brush' => 'Paint Brush',
                                'chart-trending-up' => 'Trending Up',
                                'lock-closed' => 'Lock',
                                'phone' => 'Phone',
                                'user-group' => 'User Group',
                                'check-circle' => 'Check Circle',
                                'cog' => 'Settings',
                                'document-text' => 'Document',
                                'globe' => 'Globe',
                            ])
                            ->default('check-circle'),
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->placeholder('Feature title'),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->placeholder('Brief feature description'),
                    ])
                    ->defaultItems(4)
                    ->minItems(1)
                    ->maxItems(8)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),

                Select::make('columns')
                    ->label('Number of Columns')
                    ->options([
                        '2' => '2 Columns',
                        '3' => '3 Columns',
                        '4' => '4 Columns',
                    ])
                    ->default('4'),
            ]);
    }
}
