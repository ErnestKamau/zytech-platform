<?php

namespace App\Domains\Project\Services;

use App\Core\Enums\ConstructionStage;
use App\Core\Enums\ProjectStatus;
use App\Core\Services\BaseService;
use App\Models\Project;

final class ProjectMetricsService extends BaseService
{
    /**
     * @return array{
     *     active: int,
     *     starting_soon: int,
     *     in_progress: int,
     *     near_completion: int
     * }
     */
    public function snapshot(): array
    {
        $activeQuery = $this->activeBaseQuery();

        $active = (clone $activeQuery)->count();

        $startingSoon = (clone $activeQuery)
            ->whereIn('construction_stage', [
                ConstructionStage::Planning,
                ConstructionStage::Approvals,
            ])
            ->where(function ($q): void {
                $q->whereNull('progress_percent')->orWhere('progress_percent', '<=', 0);
            })
            ->count();

        $nearCompletion = (clone $activeQuery)
            ->where(function ($q): void {
                $q->where('progress_percent', '>=', 85)
                    ->orWhereIn('construction_stage', [
                        ConstructionStage::Finishes,
                        ConstructionStage::Inspection,
                    ]);
            })
            ->count();

        $inProgress = max(0, $active - $startingSoon - $nearCompletion);

        return [
            'active' => $active,
            'starting_soon' => $startingSoon,
            'in_progress' => $inProgress,
            'near_completion' => $nearCompletion,
        ];
    }

    /**
     * Active construction projects (not archived, not completed).
     */
    private function activeBaseQuery()
    {
        return Project::query()
            ->where('status', '!=', ProjectStatus::Archived)
            ->where(function ($q): void {
                $q->whereNull('construction_stage')
                    ->orWhere('construction_stage', '!=', ConstructionStage::Completed);
            })
            ->whereNull('completed_on');
    }
}
