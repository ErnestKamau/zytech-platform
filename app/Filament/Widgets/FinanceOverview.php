<?php

namespace App\Filament\Widgets;

use App\Domains\Commerce\Services\FinanceAnalyticsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Finance';

    /**
     * @var int | array<string, ?int> | null
     */
    protected int|array|null $columns = [
        'default' => 1,
        '@sm' => 2,
        '@lg' => 3,
    ];

    protected function getStats(): array
    {
        $snapshot = app(FinanceAnalyticsService::class)->snapshot();

        return [
            Stat::make('Revenue MTD', $this->money($snapshot['revenue_mtd']))
                ->description('Completed payments this month')
                ->color('success'),
            Stat::make('Revenue 30d', $this->money($snapshot['revenue_last_30d']))
                ->description('Last 30 days')
                ->color('success'),
            Stat::make('Outstanding', $this->money($snapshot['outstanding']))
                ->description('Amount due on open invoices')
                ->color('warning'),
            Stat::make('Paid invoices', (string) $snapshot['invoices_paid'])
                ->description($snapshot['invoices_overdue'].' overdue · '.$snapshot['invoices_open'].' open · avg '.$this->money($snapshot['average_payment']))
                ->color('primary'),
        ];
    }

    private function money(float $amount): string
    {
        return 'KES '.number_format($amount, 2);
    }
}
