<?php

namespace App\Domains\Project\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Enums\MediaCollection;
use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Support\Facades\Storage;

final class SyncMilestoneMedia extends BaseAction
{
    public function handle(mixed ...$arguments): void
    {
        /** @var Project $project */
        [$project, $milestonesData] = $arguments;

        if ($milestonesData === []) {
            return;
        }

        $milestones = $project->milestones()->orderBy('sort_order')->get()->values();

        foreach (array_values($milestonesData) as $index => $row) {
            $path = $row['media_upload'] ?? null;
            $milestone = $milestones->get($index);

            if ($path === null || $path === '' || ! $milestone instanceof ProjectMilestone) {
                continue;
            }

            $absolutePath = Storage::disk('public')->path($path);

            if (! is_file($absolutePath)) {
                continue;
            }

            $milestone->addMedia($absolutePath)
                ->usingName($milestone->title)
                ->toMediaCollection(MediaCollection::MilestoneMedia->value);
        }
    }
}
