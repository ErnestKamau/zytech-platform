<?php

namespace App\Console\Commands\Projects;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\ConstructionStage;
use App\Core\Enums\MediaCollection;
use App\Core\Enums\MilestoneStatus;
use App\Core\Enums\ProjectStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Homepage\Support\HomepageCache;
use App\Domains\Project\Services\ProjectService;
use App\Models\HomepageSlide;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectMilestone;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * One-off import for the Karen Brookhouse International School project: builds the
 * project + a chronological milestone timeline from a folder of site photos/videos,
 * and publishes one of the clips as the homepage hero slide.
 */
final class ImportKarenBrookhouseProject extends Command
{
    protected $signature = 'projects:import-karen-brookhouse
        {--path=/home/ernest/Videos/zyntech : Absolute path to the folder of photos/videos}
        {--hero=compound walk.mp4 : Filename (within --path) to use as the homepage hero video}
        {--fresh : Delete any previously imported project/slide for this client before importing}';

    protected $description = 'Import the Karen Brookhouse International School project timeline and homepage hero video from a local media folder.';

    private const SLUG = 'karen-brookhouse-international-school';

    public function handle(ProjectService $projects, CacheStore $cache): int
    {
        $path = rtrim((string) $this->option('path'), '/');

        if (! is_dir($path)) {
            $this->error("Path does not exist: {$path}");

            return self::FAILURE;
        }

        $heroFilename = (string) $this->option('hero');

        if ($this->option('fresh')) {
            $this->wipeExisting();
        }

        $existing = Project::withTrashed()->where('slug', self::SLUG)->first();
        if ($existing) {
            $this->error("Project '{$existing->title}' already exists (slug: ".self::SLUG."). Re-run with --fresh to replace it.");

            return self::FAILURE;
        }

        $files = $this->scanFiles($path);

        if ($files->isEmpty()) {
            $this->error("No image/video files found in {$path}");

            return self::FAILURE;
        }

        $dated = $files->filter(fn (array $f): bool => $f['date'] !== null);
        $startedOn = $dated->min('date');
        $completedOn = $dated->max('date');

        $category = ProjectCategory::query()->firstOrCreate(
            ['slug' => 'education'],
            ['name' => 'Education', 'is_published' => true, 'sort_order' => 0],
        );

        $project = $projects->create([
            'project_category_id' => $category->id,
            'title' => 'Karen Brookhouse International School',
            'excerpt' => 'Structural and finishing works for Karen Brookhouse International School.',
            'body' => 'Structural and finishing works carried out for Karen Brookhouse International School.',
            'type' => ProjectType::Commercial,
            'status' => ProjectStatus::Published,
            'visibility' => VisibilityStatus::Public,
            'construction_stage' => ConstructionStage::Completed,
            'progress_percent' => 100,
            'client_name' => 'Karen Brookhouse International School',
            'started_on' => $startedOn,
            'completed_on' => $completedOn,
            'completion_year' => $completedOn?->year,
            'is_featured' => true,
            'published_at' => now(),
            'sort_order' => 0,
        ]);

        $this->info("Created project: {$project->title} ({$project->slug})");

        $ordered = $files->sortBy(
            fn (array $f): string => $f['date'] !== null
                ? '0_'.$f['date']->toDateTimeString()
                : '1_'.$f['name']
        )->values();

        $sortOrder = 0;
        $dateCounts = [];

        foreach ($ordered as $file) {
            $title = $this->titleFor($file, $dateCounts);

            $milestone = ProjectMilestone::query()->create([
                'project_id' => $project->id,
                'title' => $title,
                'description' => 'Progress captured on-site.',
                'stage' => $project->construction_stage->value,
                'status' => MilestoneStatus::Completed,
                'completed_on' => $file['date'] ?? $completedOn,
                'sort_order' => $sortOrder++,
            ]);

            $milestone->addMedia($file['path'])
                ->preservingOriginal()
                ->usingName($title)
                ->toMediaCollection(MediaCollection::MilestoneMedia->value);

            $this->line("  + milestone: {$title}");
        }

        $projects->persisted($project);

        $heroKey = $this->normalizeName($heroFilename);
        $heroFile = $files->first(fn (array $f): bool => $this->normalizeName($f['name']) === $heroKey);

        if ($heroFile === null) {
            $this->warn("Hero file '{$heroFilename}' not found in {$path} — skipping homepage slide.");
        } else {
            $slide = HomepageSlide::query()->create([
                'headline' => 'Built on Kenyan soil, engineered to last.',
                'eyebrow' => 'Karen Brookhouse International School',
                'alt_text' => 'Karen Brookhouse International School compound walkthrough',
                'cta_label' => 'View Projects',
                'cta_url' => '/projects',
                'is_active' => true,
                'sort_order' => (int) (HomepageSlide::query()->min('sort_order') ?? 0) - 1,
            ]);

            $slide->addMedia($heroFile['path'])
                ->preservingOriginal()
                ->usingName($slide->alt_text)
                ->toMediaCollection(MediaCollection::HomepageSlideMedia->value);

            $cache->forget(HomepageCache::SLIDES);

            $this->info("Created homepage hero slide from: {$heroFile['name']}");
        }

        $this->info('Done.');

        return self::SUCCESS;
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{path: string, name: string, date: ?Carbon}>
     */
    private function scanFiles(string $path): \Illuminate\Support\Collection
    {
        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'mov'];

        return collect(scandir($path) ?: [])
            ->reject(fn (string $name): bool => in_array($name, ['.', '..'], true))
            ->filter(fn (string $name): bool => in_array(strtolower(pathinfo($name, PATHINFO_EXTENSION)), $extensions, true))
            ->map(fn (string $name): array => [
                'path' => $path.'/'.$name,
                'name' => $name,
                'date' => $this->extractDate($name),
            ])
            ->values();
    }

    private function normalizeName(string $filename): string
    {
        return Str::slug(pathinfo($filename, PATHINFO_FILENAME));
    }

    private function extractDate(string $filename): ?Carbon
    {
        if (! preg_match('/(\d{4}-\d{2}-\d{2})[\s-]+at[\s-]+(\d{1,2}\.\d{2}\.\d{2})[\s-]*(AM|PM)/i', $filename, $m)) {
            return null;
        }

        $time = str_replace('.', ':', $m[2]);

        try {
            return Carbon::createFromFormat('Y-m-d g:i:s A', "{$m[1]} {$time} {$m[3]}");
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param  array{path: string, name: string, date: ?Carbon}  $file
     * @param  array<string, int>  $dateCounts
     */
    private function titleFor(array $file, array &$dateCounts): string
    {
        if ($file['date'] === null) {
            return Str::of(pathinfo($file['name'], PATHINFO_FILENAME))
                ->replace(['_', '-'], ' ')
                ->title()
                ->toString();
        }

        $key = $file['date']->toDateString();
        $dateCounts[$key] = ($dateCounts[$key] ?? 0) + 1;
        $label = 'Site visit — '.$file['date']->format('j M Y');

        return $dateCounts[$key] > 1 ? "{$label} ({$dateCounts[$key]})" : $label;
    }

    private function wipeExisting(): void
    {
        $project = Project::withTrashed()->where('slug', self::SLUG)->first();

        if ($project) {
            foreach ($project->milestones()->withTrashed()->get() as $milestone) {
                $milestone->clearMediaCollection(MediaCollection::MilestoneMedia->value);
            }
            $project->milestones()->withTrashed()->forceDelete();
            $project->forceDelete();
            $this->warn("Deleted existing project '{$project->title}' and its milestones.");
        }

        HomepageSlide::withTrashed()
            ->where('eyebrow', 'Karen Brookhouse International School')
            ->get()
            ->each(function (HomepageSlide $slide): void {
                $slide->clearMediaCollection(MediaCollection::HomepageSlideMedia->value);
                $slide->clearMediaCollection(MediaCollection::HomepageSlidePoster->value);
                $slide->forceDelete();
            });
    }
}
