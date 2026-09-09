<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BusinessOverviewWidget;
use App\Filament\Widgets\NeedsAttentionWidget;
use App\Filament\Widgets\OrderOverviewWidget;
use App\Filament\Widgets\ProjectOverviewWidget;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\RevenueChartWidget;
use App\Filament\Widgets\SalesPipelineWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = 1;

    public function getWidgets(): array
    {
        return [
            BusinessOverviewWidget::class,
            RevenueChartWidget::class,
            SalesPipelineWidget::class,
            NeedsAttentionWidget::class,
            OrderOverviewWidget::class,
            ProjectOverviewWidget::class,
            RecentActivityWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 2,
        ];
    }
}
