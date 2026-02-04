<?php

declare(strict_types=1);

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

/**
 * Rich text block for general WYSIWYG content.
 *
 * Supports headings, lists, links, and basic formatting.
 */
class RichTextBlock extends PageBlock
{
    /**
     * Get the block schema for the Filament form builder.
     */
    public static function getBlockSchema(): Block
    {
        return Block::make('rich-text')
            ->label('Rich Text')
            ->icon('heroicon-o-document-text')
            ->schema([
                TextInput::make('heading')
                    ->label('Section Heading (optional)')
                    ->placeholder('Leave empty for no heading'),

                RichEditor::make('content')
                    ->label('Content')
                    ->required()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'link',
                        'orderedList',
                        'bulletList',
                        'h2',
                        'h3',
                        'blockquote',
                        'redo',
                        'undo',
                    ])
                    ->columnSpanFull(),

                Select::make('width')
                    ->label('Content Width')
                    ->options([
                        'narrow' => 'Narrow (672px)',
                        'default' => 'Default (1024px)',
                        'wide' => 'Wide (1280px)',
                    ])
                    ->default('default'),

                Select::make('alignment')
                    ->label('Text Alignment')
                    ->options([
                        'left' => 'Left',
                        'center' => 'Center',
                    ])
                    ->default('left'),
            ]);
    }
}
