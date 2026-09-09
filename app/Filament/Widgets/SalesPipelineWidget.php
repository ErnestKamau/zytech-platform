<?php

namespace App\Filament\Widgets;

use App\Domains\Quotation\Services\SalesAnalyticsService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SalesPipelineWidget extends Widget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected string $view = 'filament.widgets.sales-pipeline';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user !== null && ($user->can('quotations.view') || $user->can('quotations.manage'));
    }

    /**
     * @return array{
     *     stages: list<array{key: string, label: string, count: int, value: float|null}>,
     *     conversions: array<string, float>,
     *     period_days: int
     * }
     */
    public function getFunnel(): array
    {
        return app(SalesAnalyticsService::class)->funnel();
    }
}
