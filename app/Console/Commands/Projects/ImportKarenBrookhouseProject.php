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

/**
 * One-off import for the Karen Brookhouse International School project.
 *
 * The source folder has WhatsApp re-sends of the same clips under different
 * filenames/timestamps (confirmed by content hash), so this uses a curated,
 * deduplicated manifest — one entry per distinct piece of footage — with
 * titles written from actually reviewing each photo/video frame, instead of
 * auto-generating generic "Site visit" labels from filename timestamps.
 */
final class ImportKarenBrookhouseProject extends Command
{
    protected $signature = 'projects:import-karen-brookhouse
        {--path=/home/ernest/Videos/zyntech : Absolute path to the folder of photos/videos}
        {--fresh : Delete any previously imported project/slide for this client before importing}';

    protected $description = 'Import the Karen Brookhouse International School project timeline and homepage hero video from a local media folder.';

    private const SLUG = 'karen-brookhouse-international-school';

    /**
     * One row per distinct piece of content. `date` is null where no capture
     * is known to within a real day (used for exactly one clip: compound-work.mp4).
     *
     * @var list<array{file: string, title: string, description: string, date: ?string, hero?: bool}>
     */
    private const MANIFEST = [
        ['file' => 'WhatsApp-Image-2026-06-24-at-12.43.31-PM.jpeg', 'title' => 'Site clearance — ballast and compound path laid', 'description' => 'Gravel bedding laid for the compound access path.', 'date' => '2026-06-24'],
        ['file' => 'WhatsApp-Image-2026-06-24-at-12.43.31-PM-(1).jpeg', 'title' => 'Compound grading and landscaping underway', 'description' => 'Grading and hedge planting in progress around the gravel bed.', 'date' => '2026-06-24'],
        ['file' => 'compound-work.mp4', 'title' => 'Compound ground works in progress', 'description' => 'Open compound area during ground preparation.', 'date' => null],
        ['file' => 'WhatsApp-Image-2026-06-24-at-12.48.31-PM.jpeg', 'title' => 'Courtyard seating area completed', 'description' => 'Finished paved courtyard with seating and shade umbrellas.', 'date' => '2026-06-24'],
        ['file' => 'WhatsApp-Image-2026-07-18-at-4.10.24-AM.jpeg', 'title' => 'Covered walkway structure under construction', 'description' => 'Steel roof framework being erected over the connecting walkway.', 'date' => '2026-07-18'],
        ['file' => 'WhatsApp-Video-2026-07-18-at-4.10.24-AM-(1).mp4', 'title' => 'Interior strip-out and demolition', 'description' => 'Old flooring and tiling removed ahead of refinishing.', 'date' => '2026-07-18'],
        ['file' => 'WhatsApp-Video-2026-07-18-at-4.10.24-AM-(2).mp4', 'title' => 'Scaffolding erected on exterior wall', 'description' => 'Scaffolding set up against the building facade for exterior works.', 'date' => '2026-07-18'],
        ['file' => 'WhatsApp-Video-2026-07-18-at-4.10.24-AM.mp4', 'title' => 'Finished interior — furniture installed', 'description' => 'Completed room with furniture in place.', 'date' => '2026-07-18'],
        ['file' => 'WhatsApp-Video-2026-09-12-at-11.59.24-AM.mp4', 'title' => 'Door and timber fittings ready for installation', 'description' => 'Timber doors and fittings staged for fitting.', 'date' => '2026-09-12'],
        ['file' => 'WhatsApp-Image-2026-09-12-at-11.59.22-AM.jpeg', 'title' => 'Completed two-storey building exterior', 'description' => 'Finished exterior with balustrades and landscaping.', 'date' => '2026-09-12'],
        ['file' => 'interior.mp4', 'title' => 'Completed washroom', 'description' => 'Finished washroom with fittings installed.', 'date' => '2026-09-12'],
        ['file' => 'compound-walk.mp4', 'title' => 'Completed covered walkway', 'description' => 'Finished covered walkway connecting the building wings.', 'date' => '2026-09-12', 'hero' => true],
    ];

    public function handle(ProjectService $projects, CacheStore $cache): int
    {
        $path = rtrim((string) $this->option('path'), '/');

        if (! is_dir($path)) {
            $this->error("Path does not exist: {$path}");

            return self::FAILURE;
        }

        $missing = collect(self::MANIFEST)->reject(fn (array $row): bool => is_file($path.'/'.$row['file']));
        if ($missing->isNotEmpty()) {
            foreach ($missing as $row) {
                $this->error("Missing file: {$row['file']}");
            }

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->wipeExisting();
        }

        $existing = Project::withTrashed()->where('slug', self::SLUG)->first();
        if ($existing) {
            $this->error("Project '{$existing->title}' already exists (slug: ".self::SLUG."). Re-run with --fresh to replace it.");

            return self::FAILURE;
        }

        $dates = collect(self::MANIFEST)->pluck('date')->filter();
        $startedOn = $dates->min();
        $completedOn = $dates->max();

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
            'completion_year' => $completedOn ? (int) substr($completedOn, 0, 4) : null,
            'is_featured' => true,
            'published_at' => now(),
            'sort_order' => 0,
        ]);

        $this->info("Created project: {$project->title} ({$project->slug})");

        $sortOrder = 0;
        $heroRow = null;

        foreach (self::MANIFEST as $row) {
            $milestone = ProjectMilestone::query()->create([
                'project_id' => $project->id,
                'title' => $row['title'],
                'description' => $row['description'],
                'stage' => $project->construction_stage->value,
                'status' => MilestoneStatus::Completed,
                'completed_on' => $row['date'] ?? $completedOn,
                'sort_order' => $sortOrder++,
            ]);

            $milestone->addMedia($path.'/'.$row['file'])
                ->preservingOriginal()
                ->usingName($row['title'])
                ->toMediaCollection(MediaCollection::MilestoneMedia->value);

            $this->line("  + milestone: {$row['title']}");

            if (! empty($row['hero'])) {
                $heroRow = $row;
            }
        }

        $projects->persisted($project);

        if ($heroRow === null) {
            $this->warn('No manifest row flagged `hero` — skipping homepage slide.');
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

            $slide->addMedia($path.'/'.$heroRow['file'])
                ->preservingOriginal()
                ->usingName($slide->alt_text)
                ->toMediaCollection(MediaCollection::HomepageSlideMedia->value);

            $cache->forget(HomepageCache::SLIDES);

            $this->info("Created homepage hero slide from: {$heroRow['file']}");
        }

        $this->info('Done.');

        return self::SUCCESS;
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
