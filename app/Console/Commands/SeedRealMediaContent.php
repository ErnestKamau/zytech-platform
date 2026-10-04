<?php

namespace App\Console\Commands;

use App\Core\Enums\MediaCollection;
use App\Domains\Project\Services\ProjectService;
use App\Models\HomepageSlide;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SeedRealMediaContent extends Command
{
    protected $signature = 'zytech:seed-real-media {--source=new videos and images}';

    protected $description = 'Attach the real seed videos/photo into homepage slides and a project\'s timeline.';

    public function handle(): int
    {
        $source = base_path($this->option('source'));

        if (! is_dir($source)) {
            $this->error("Source directory not found: {$source}");

            return self::FAILURE;
        }

        $photo = collect(File::files($source))
            ->first(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png'], true));

        $videos = collect(File::files($source))
            ->filter(fn ($file) => strtolower($file->getExtension()) === 'mp4')
            ->values();

        if ($videos->isEmpty() && ! $photo) {
            $this->error('No media files found in source directory.');

            return self::FAILURE;
        }

        foreach ($videos as $index => $video) {
            $slide = HomepageSlide::create([
                'headline' => null,
                'alt_text' => 'Zytech construction site footage',
                'is_active' => true,
                'sort_order' => $index,
            ]);

            $slide->addMedia($video->getPathname())
                ->preservingOriginal()
                ->toMediaCollection(MediaCollection::HomepageSlideMedia->value);

            $this->line("Created homepage slide for {$video->getFilename()}");
        }

        if ($photo) {
            $slide = HomepageSlide::create([
                'headline' => null,
                'alt_text' => 'Zytech completed residential exterior',
                'is_active' => true,
                'sort_order' => $videos->count(),
            ]);

            $slide->addMedia($photo->getPathname())
                ->preservingOriginal()
                ->toMediaCollection(MediaCollection::HomepageSlideMedia->value);

            $this->line("Created homepage slide for {$photo->getFilename()}");
        }

        $project = Project::query()->whereHas('milestones')->first()
            ?? Project::query()->first();

        if ($project) {
            $milestones = $project->milestones()->orderBy('sort_order')->get();
            $assets = $videos->map(fn ($file) => $file->getPathname())
                ->push($photo?->getPathname())
                ->filter()
                ->values();

            foreach ($milestones as $index => $milestone) {
                $asset = $assets->get($index) ?? $assets->first();

                if (! $asset) {
                    continue;
                }

                $milestone->addMedia($asset)
                    ->preservingOriginal()
                    ->toMediaCollection(MediaCollection::MilestoneMedia->value);

                $this->line("Attached media to milestone: {$milestone->title}");
            }

            app(ProjectService::class)->forget($project->slug);
            $this->line("Busted project cache for: {$project->title}");
        } else {
            $this->warn('No project found to attach milestone media to.');
        }

        $this->info('Seeded homepage slides and milestone media from real files.');

        return self::SUCCESS;
    }
}
