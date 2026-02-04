<?php

declare(strict_types=1);

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

/**
 * Call-to-action block for conversion sections.
 *
 * Centered design with heading, description, and buttons.
 */
class CTABlock extends PageBlock
{
    /**
     * Get the block schema for the Filament form builder.
     */
    public static function getBlockSchema(): Block
    {
        return Block::make('cta')
            ->label('Call to Action')
            ->icon('heroicon-o-megaphone')
            ->schema([
                TextInput::make('heading')
                    ->label('Heading')
                    ->required()
                    ->placeholder('e.g., Ready to Brand Your Calls?'),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->placeholder('Supporting text for the CTA'),

                TextInput::make('primary_cta_text')
                    ->label('Primary Button Text')
                    ->placeholder('e.g., Start Free Trial'),

                TextInput::make('primary_cta_url')
                    ->label('Primary Button URL')
                    ->placeholder('/register'),

                TextInput::make('secondary_cta_text')
                    ->label('Secondary Button Text')
                    ->placeholder('e.g., Contact Sales'),

                TextInput::make('secondary_cta_url')
                    ->label('Secondary Button URL')
                    ->placeholder('/contact'),

                Select::make('style')
                    ->label('Style')
                    ->options([
                        'card' => 'Card (with background)',
                        'simple' => 'Simple (no background)',
                    ])
                    ->default('card'),
            ]);
    }
}
