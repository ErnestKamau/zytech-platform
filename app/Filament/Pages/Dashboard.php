<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CompanyStatisticsOverview;
use App\Filament\Widgets\FinanceOverview;
use App\Filament\Widgets\NeedsAttention;
use App\Filament\Widgets\SalesFunnelOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = 1;

    public function getWidgets(): array
    {
        return [
            CompanyStatisticsOverview::class,
            FinanceOverview::class,
            SalesFunnelOverview::class,
            NeedsAttention::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 1;
    }
}
