<?php

namespace App\Filament\Widgets;

use App\Domains\Project\Services\ProjectMetricsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ProjectOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 6;

    protected ?string $heading = 'Projects';

    protected int|string|array $columnSpan = 1;

    /**
     * @var int | array<string, ?int> | null
     */
    protected int|array|null $columns = 2;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user !== null && ($user->can('projects.view') || $user->can('projects.manage'));
    }

    protected function getStats(): array
    {
        $snapshot = app(ProjectMetricsService::class)->snapshot();

        return [
            Stat::make('Active', (string) $snapshot['active'])
                ->description('Not completed')
                ->color('primary'),
            Stat::make('Starting soon', (string) $snapshot['starting_soon'])
                ->description('Planning / approvals')
                ->color('gray'),
            Stat::make('In progress', (string) $snapshot['in_progress'])
                ->description('Under construction')
                ->color('info'),
            Stat::make('Near completion', (string) $snapshot['near_completion'])
                ->description('≥85% or finishes stage')
                ->color('success'),
        ];
    }
}
