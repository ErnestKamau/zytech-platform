<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Domains\Project\Actions\SyncMilestoneMedia;
use App\Domains\Project\Services\ProjectService;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProjects extends ManageRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->after(function (array $data, Project $record): void {
                app(SyncMilestoneMedia::class)->handle($record, $data['milestones'] ?? []);
                app(ProjectService::class)->persisted($record, created: true);
            }),
        ];
    }
}
