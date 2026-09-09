<?php

namespace App\Filament\Widgets;

use App\Domains\Commerce\Services\FinanceAnalyticsService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RevenueChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Revenue — Last 30 days';

    protected ?string $description = 'Completed payments by day';

    protected ?string $maxHeight = '280px';

    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user !== null && ($user->can('commerce.view') || $user->can('commerce.manage'));
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $series = app(FinanceAnalyticsService::class)->revenueSeries(30);

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (KES)',
                    'data' => $series['values'],
                    'fill' => false,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $series['labels'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
