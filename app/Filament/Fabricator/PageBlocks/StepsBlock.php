<?php

declare(strict_types=1);

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

/**
 * Steps block for "How It Works" sections.
 *
 * Displays numbered steps with connector lines.
 */
class StepsBlock extends PageBlock
{
    /**
     * Get the block schema for the Filament form builder.
     */
    public static function getBlockSchema(): Block
    {
        return Block::make('steps')
            ->label('Steps / How It Works')
            ->icon('heroicon-o-list-bullet')
            ->schema([
                TextInput::make('eyebrow')
                    ->label('Eyebrow Text')
                    ->placeholder('e.g., How It Works'),

                TextInput::make('heading')
                    ->label('Section Heading')
                    ->placeholder('e.g., Get Started in Minutes'),

                Textarea::make('description')
                    ->label('Section Description')
                    ->rows(2),

                Repeater::make('steps')
                    ->label('Steps')
                    ->schema([
                        TextInput::make('title')
                            ->label('Step Title')
                            ->required()
                            ->placeholder('e.g., Create Account'),
                        Textarea::make('description')
                            ->label('Step Description')
                            ->rows(2)
                            ->placeholder('Brief description of this step'),
                    ])
                    ->defaultItems(4)
                    ->minItems(2)
                    ->maxItems(6)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),

                Toggle::make('show_connectors')
                    ->label('Show Connector Lines')
                    ->helperText('Display lines between steps on desktop')
                    ->default(true),

                Toggle::make('auto_number')
                    ->label('Auto-Number Steps')
                    ->helperText('Automatically add 01, 02, etc.')
                    ->default(true),
            ]);
    }
}
