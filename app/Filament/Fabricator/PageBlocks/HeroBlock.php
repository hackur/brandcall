<?php

declare(strict_types=1);

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

/**
 * Hero section block for landing pages.
 *
 * Supports rotating headlines, subheadline, CTA buttons,
 * and configurable background styles.
 */
class HeroBlock extends PageBlock
{
    /**
     * Get the block schema for the Filament form builder.
     */
    public static function getBlockSchema(): Block
    {
        return Block::make('hero')
            ->label('Hero Section')
            ->icon('heroicon-o-sparkles')
            ->schema([
                TextInput::make('eyebrow')
                    ->label('Eyebrow Text')
                    ->placeholder('e.g., Trusted by 500+ Businesses')
                    ->helperText('Small text above the headline'),

                Repeater::make('headlines')
                    ->label('Rotating Headlines')
                    ->schema([
                        TextInput::make('title')
                            ->label('Main Title')
                            ->required()
                            ->placeholder('e.g., Branded Caller ID'),
                        TextInput::make('subtitle')
                            ->label('Subtitle')
                            ->required()
                            ->placeholder('e.g., That Builds Trust'),
                    ])
                    ->defaultItems(1)
                    ->minItems(1)
                    ->maxItems(6)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->placeholder('Supporting text that appears below the headline'),

                TextInput::make('primary_cta_text')
                    ->label('Primary Button Text')
                    ->placeholder('e.g., Start Free Trial'),

                TextInput::make('primary_cta_url')
                    ->label('Primary Button URL')
                    ->placeholder('/register'),

                TextInput::make('secondary_cta_text')
                    ->label('Secondary Button Text')
                    ->placeholder('e.g., Watch Demo'),

                TextInput::make('secondary_cta_url')
                    ->label('Secondary Button URL')
                    ->placeholder('#demo'),

                Select::make('background_style')
                    ->label('Background Style')
                    ->options([
                        'gradient' => 'Subtle Gradient',
                        'solid' => 'Solid Dark',
                        'glow' => 'With Accent Glow',
                    ])
                    ->default('glow'),

                Toggle::make('show_indicators')
                    ->label('Show Headline Indicators')
                    ->helperText('Display dots for headline rotation')
                    ->default(true),
            ]);
    }
}
