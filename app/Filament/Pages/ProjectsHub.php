<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ProjectCategories\ProjectCategoryResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use Filament\Support\Icons\Heroicon;

class ProjectsHub extends AdminHubPage
{
    protected static ?string $navigationLabel = 'Projects';

    protected static ?string $title = 'Projects';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?int $navigationSort = 8;

    protected function getHubSubheading(): ?string
    {
        return 'Portfolio projects and categories.';
    }

    public function getHubSections(): array
    {
        return [
            [
                'title' => 'Portfolio',
                'cards' => [
                    $this->hubCard('Projects', ProjectResource::class, Heroicon::OutlinedBriefcase, count: Project::query()->count()),
                    $this->hubCard('Categories', ProjectCategoryResource::class, Heroicon::OutlinedTag),
                ],
            ],
        ];
    }
}
