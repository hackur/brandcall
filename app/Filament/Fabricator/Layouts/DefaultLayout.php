<?php

declare(strict_types=1);

namespace App\Filament\Fabricator\Layouts;

use Z3d0X\FilamentFabricator\Layouts\Layout;

/**
 * Default page layout for BrandCall marketing pages.
 *
 * Wraps content with consistent header, footer, and base styles.
 */
class DefaultLayout extends Layout
{
    /**
     * Get the unique identifier for this layout.
     */
    protected static ?string $name = 'default';

    /**
     * Get the display label for this layout.
     */
    public static function getLabel(): string
    {
        return 'Default Layout';
    }
}
