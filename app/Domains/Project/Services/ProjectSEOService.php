<?php

namespace App\Domains\Project\Services;

use App\Core\Services\BaseService;
use App\Domains\Project\Data\ProjectData;
use App\Models\Project;
use Illuminate\Support\Str;

final class ProjectSEOService extends BaseService
{
    public function ensure(Project $project): Project
    {
        $title = filled($project->meta_title)
            ? $project->meta_title
            : $this->titleFor($project);

        $description = filled($project->meta_description)
            ? $project->meta_description
            : $this->descriptionFor($project);

        $ogImage = filled($project->og_image_key)
            ? $project->og_image_key
            : $project->image_key;

        if (
            $project->meta_title === $title
            && $project->meta_description === $description
            && $project->og_image_key === $ogImage
        ) {
            return $project;
        }

        $project->forceFill([
            'meta_title' => $title,
            'meta_description' => $description,
            'og_image_key' => $ogImage,
        ])->save();

        return $project->refresh();
    }

    /**
     * @return array{title: string, description: string, og_image_key: ?string}
     */
    public function forPage(ProjectData $project): array
    {
        return [
            'title' => $project->metaTitle !== ''
                ? $project->metaTitle
                : $project->title.' — Zytech Contractors',
            'description' => $project->metaDescription !== ''
                ? $project->metaDescription
                : Str::limit($project->excerpt !== '' ? $project->excerpt : $project->body, 160),
            'og_image_key' => $project->ogImageKey ?? $project->imageKey,
        ];
    }

    private function titleFor(Project $project): string
    {
        return $project->title.' — Zytech Contractors';
    }

    private function descriptionFor(Project $project): string
    {
        $source = filled($project->excerpt) ? $project->excerpt : (string) $project->body;

        return Str::limit(trim(strip_tags($source)), 160);
    }
}
