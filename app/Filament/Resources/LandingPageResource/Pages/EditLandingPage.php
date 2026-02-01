<?php

declare(strict_types=1);

namespace App\Filament\Resources\LandingPageResource\Pages;

use App\Filament\Resources\LandingPageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandingPage extends EditRecord
{
    protected static string $resource = LandingPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->icon('heroicon-o-eye')
                ->url(fn () => "/lp/{$this->record->slug}")
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }
}
