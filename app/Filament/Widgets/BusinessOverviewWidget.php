<?php

namespace App\Filament\Widgets;

use App\Domains\Commerce\Services\FinanceAnalyticsService;
use App\Domains\Project\Services\ProjectMetricsService;
use App\Domains\Quotation\Services\SalesAnalyticsService;
use App\Support\Helpers\MoneyFormatter;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class BusinessOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Business overview';

    protected int|string|array $columnSpan = 'full';

    /**
     * @var int | array<string, ?int> | null
     */
    protected int|array|null $columns = [
        'default' => 1,
        '@sm' => 2,
        '@lg' => 4,
    ];

    public static function canView(): bool
    {
        $user = Auth::user();

        if ($user === null) {
            return false;
        }

        return $user->can('commerce.view')
            || $user->can('commerce.manage')
            || $user->can('quotations.view')
            || $user->can('quotations.manage')
            || $user->can('projects.view')
            || $user->can('projects.manage');
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        $stats = [];

        if ($user?->can('commerce.view') || $user?->can('commerce.manage')) {
            $finance = app(FinanceAnalyticsService::class)->snapshot();
            $stats[] = Stat::make('Revenue MTD', MoneyFormatter::format($finance['revenue_mtd']))
                ->description($finance['revenue_mtd'] > 0
                    ? 'Collected this month'
                    : 'No revenue recorded this month')
                ->color('success');
            $stats[] = Stat::make('Outstanding', MoneyFormatter::format($finance['outstanding']))
                ->description($finance['invoices_open'].' open invoices')
                ->color('warning');
        }

        if ($user?->can('quotations.view') || $user?->can('quotations.manage')) {
            $pipeline = app(SalesAnalyticsService::class)->pipelineValue();
            $openQuotes = app(SalesAnalyticsService::class)->snapshot()['quotations_pipeline'];
            $stats[] = Stat::make('Sales pipeline', MoneyFormatter::compact($pipeline))
                ->description($openQuotes.' open opportunities')
                ->color('primary');
        }

        if ($user?->can('projects.view') || $user?->can('projects.manage')) {
            $projects = app(ProjectMetricsService::class)->snapshot();
            $stats[] = Stat::make('Active projects', (string) $projects['active'])
                ->description($projects['near_completion'].' near completion')
                ->color('info');
        }

        return $stats;
    }
}
