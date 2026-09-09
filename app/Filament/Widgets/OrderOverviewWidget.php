<?php

namespace App\Filament\Widgets;

use App\Domains\Commerce\Services\OrderMetricsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class OrderOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = 'Orders';

    protected int|string|array $columnSpan = 1;

    /**
     * @var int | array<string, ?int> | null
     */
    protected int|array|null $columns = 2;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user !== null && ($user->can('commerce.view') || $user->can('commerce.manage'));
    }

    protected function getStats(): array
    {
        $snapshot = app(OrderMetricsService::class)->snapshot();

        return [
            Stat::make('Today', (string) $snapshot['today'])
                ->description('Orders placed today')
                ->color('primary'),
            Stat::make('MTD', (string) $snapshot['mtd'])
                ->description('This month')
                ->color('primary'),
            Stat::make('Pending fulfilment', (string) $snapshot['pending_fulfilment'])
                ->description($snapshot['pending_fulfilment'] === 0 ? 'Nothing waiting' : 'Needs action')
                ->color($snapshot['pending_fulfilment'] > 0 ? 'warning' : 'success'),
            Stat::make('Completed', (string) $snapshot['completed'])
                ->description($snapshot['cancelled'].' cancelled')
                ->color('success'),
        ];
    }
}
