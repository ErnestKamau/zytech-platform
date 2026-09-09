<?php

namespace App\Filament\Widgets;

use App\Domains\Quotation\Services\SalesAnalyticsService;
use Filament\Widgets\Widget;

class SalesFunnelOverview extends Widget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.sales-funnel-overview';

    /**
     * @return array{
     *     stages: list<array{key: string, label: string, count: int}>,
     *     conversions: array<string, float>
     * }
     */
    public function getFunnel(): array
    {
        return app(SalesAnalyticsService::class)->funnel();
    }
}
