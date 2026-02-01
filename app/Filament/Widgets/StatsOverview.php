<?php

namespace App\Filament\Widgets;

use App\Models\Brand;
use App\Models\CallLog;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $now = Carbon::now();

        return [
            Stat::make('Total Tenants', Tenant::count())
                ->description('Active customer accounts')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Total Users', User::count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Total Brands', Brand::withoutGlobalScopes()->count())
                ->description('Branded caller IDs')
                ->descriptionIcon('heroicon-m-identification')
                ->color('warning'),

            Stat::make('Calls Today', CallLog::withoutGlobalScopes()
                ->whereDate('created_at', $now->toDateString())
                ->count())
                ->description('Branded calls made today')
                ->descriptionIcon('heroicon-m-phone')
                ->color('info'),
        ];
    }
}
