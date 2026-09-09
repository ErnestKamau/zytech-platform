<?php

namespace App\Filament\Widgets;

use App\Domains\Company\Services\CompanyService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CompanyStatisticsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Company stats';

    protected ?string $description = 'CMS website statistics (not traffic or clicks).';

    /**
     * Kept for Company CMS hub use — not shown on the operational admin dashboard.
     */
    public static function canView(): bool
    {
        return false;
    }

    /**
     * @var int | array<string, ?int> | null
     */
    protected int|array|null $columns = [
        'default' => 1,
        '@sm' => 2,
        '@xl' => 4,
    ];

    protected function getStats(): array
    {
        $statistics = app(CompanyService::class)->adminStatistics();

        if ($statistics->isEmpty()) {
            return [
                Stat::make('No statistics yet', '—')
                    ->description('Add visible statistics under Company → Statistics'),
            ];
        }

        return $statistics
            ->take(4)
            ->map(fn ($statistic): Stat => Stat::make($statistic->label, $statistic->value))
            ->all();
    }
}
