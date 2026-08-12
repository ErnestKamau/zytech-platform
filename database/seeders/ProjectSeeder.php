<?php

namespace Database\Seeders;

use App\Core\Enums\ConstructionStage;
use App\Core\Enums\MilestoneStatus;
use App\Core\Enums\ProjectStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Project\Services\ProjectService;
use App\Models\Project;
use App\Models\ProjectBeforeAfter;
use App\Models\ProjectCategory;
use App\Models\ProjectGalleryItem;
use App\Models\ProjectMilestone;
use App\Models\ProjectStatistic;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $categories = $this->seedCategories();
        $services = Service::query()->pluck('id', 'slug');
        $this->seedProjects($categories, $services);

        app(ProjectService::class)->forget();
    }

    /**
     * @return array<string, ProjectCategory>
     */
    private function seedCategories(): array
    {
        $rows = [
            ['name' => 'Commercial', 'slug' => 'commercial', 'description' => 'Courtyards, facades, and commercial finishes.', 'sort_order' => 0],
            ['name' => 'Site preparation', 'slug' => 'site-preparation', 'description' => 'Groundworks, ballast, and platform builds.', 'sort_order' => 1],
            ['name' => 'Paving', 'slug' => 'paving', 'description' => 'Hardscaping, pavers, and gravel leveling.', 'sort_order' => 2],
            ['name' => 'Structural', 'slug' => 'structural', 'description' => 'Steel frames, walkways, and pergolas.', 'sort_order' => 3],
            ['name' => 'Residential', 'slug' => 'residential', 'description' => 'Homes, courtyards, and exterior finishes.', 'sort_order' => 4],
            ['name' => 'Landscaping', 'slug' => 'landscaping', 'description' => 'Garden walkways and outdoor rooms.', 'sort_order' => 5],
        ];

        $categories = [];

        foreach ($rows as $row) {
            $categories[$row['slug']] = ProjectCategory::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [...$row, 'is_published' => true],
            );
        }

        return $categories;
    }

    /**
     * @param  array<string, ProjectCategory>  $categories
     * @param  Collection<string, string>  $services
     */
    private function seedProjects(array $categories, $services): void
    {
        $coords = [
            [-1.2865, 36.8172],
            [-1.2921, 36.8219],
            [-1.2789, 36.8325],
            [-1.3012, 36.8078],
            [-1.2654, 36.8411],
            [-1.3103, 36.7988],
        ];

        foreach ($this->catalogue() as $index => $row) {
            [$lat, $lng] = $coords[$index % count($coords)];

            $project = Project::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'project_category_id' => $categories[$row['category']]->id,
                    'title' => $row['title'],
                    'excerpt' => $row['excerpt'],
                    'body' => $row['body'],
                    'case_study' => $row['case_study'],
                    'image_key' => $row['image'],
                    'video_key' => $row['video'] ?? null,
                    'type' => $row['type'],
                    'status' => ProjectStatus::Published,
                    'visibility' => VisibilityStatus::Public,
                    'construction_stage' => $row['stage'],
                    'progress_percent' => $row['progress'],
                    'county' => $row['county'],
                    'city' => $row['city'],
                    'location_label' => $row['city'].', '.$row['county'],
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'completion_year' => $row['completion_year'] ?? null,
                    'is_featured' => $row['featured'],
                    'meta_title' => $row['title'].' — Zytech Contractors',
                    'meta_description' => $row['excerpt'],
                    'og_image_key' => $row['image'],
                    'published_at' => now(),
                    'sort_order' => $index,
                ],
            );

            $this->syncChildren($project, $row);

            $serviceIds = collect($row['services'] ?? [])
                ->map(fn (string $slug): ?string => $services[$slug] ?? null)
                ->filter()
                ->values()
                ->all();

            if ($serviceIds !== []) {
                $project->services()->sync(
                    collect($serviceIds)->mapWithKeys(fn (string $id, int $order): array => [$id => ['sort_order' => $order]])->all(),
                );
            }
        }
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function syncChildren(Project $project, array $row): void
    {
        foreach ($row['gallery'] ?? [] as $order => $imageKey) {
            ProjectGalleryItem::query()->updateOrCreate(
                ['project_id' => $project->id, 'image_key' => $imageKey],
                ['sort_order' => $order],
            );
        }

        foreach ($row['milestones'] ?? [] as $order => $milestone) {
            ProjectMilestone::query()->updateOrCreate(
                ['project_id' => $project->id, 'title' => $milestone['title']],
                [
                    'description' => $milestone['description'],
                    'status' => $milestone['status'],
                    'sort_order' => $order,
                ],
            );
        }

        foreach ($row['stats'] ?? [] as $order => $stat) {
            ProjectStatistic::query()->updateOrCreate(
                ['project_id' => $project->id, 'label' => $stat['label']],
                ['value' => $stat['value'], 'is_visible' => true, 'sort_order' => $order],
            );
        }

        foreach ($row['before_after'] ?? [] as $order => $comparison) {
            ProjectBeforeAfter::query()->updateOrCreate(
                [
                    'project_id' => $project->id,
                    'before_image_key' => $comparison['before'],
                    'after_image_key' => $comparison['after'],
                ],
                [
                    'caption' => $comparison['caption'] ?? null,
                    'description' => $comparison['description'] ?? null,
                    'sort_order' => $order,
                ],
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function catalogue(): array
    {
        $config = collect(config('zyntech-projects'))->keyBy('slug');

        return [
            $this->entry($config['commercial-courtyard'], [
                'excerpt' => 'Stone cladding, interlocking pavers, and shade structures built as one exterior room.',
                'body' => 'A commercial courtyard delivered as a single package: drainage, base, pavers, stone cladding, and shade. One Zytech crew owned the programme from groundworks to handover.',
                'case_study' => 'The client needed a courtyard that could handle Nairobi rain without pooling, stay cool under midday sun, and read as part of the building — not an afterthought. We sequenced drainage first, then base and edge restraints, then finishes and planting pockets.',
                'stage' => ConstructionStage::Completed,
                'services' => ['exterior-design', 'paving-hardscaping', 'construction-management'],
                'gallery' => ['commercial_courtyard', 'paving_gravel_leveling'],
                'milestones' => [
                    ['title' => 'Platform & drainage', 'description' => 'Falls locked and base compacted.', 'status' => MilestoneStatus::Completed],
                    ['title' => 'Pavers & stone', 'description' => 'Cladding and paving laid to pattern.', 'status' => MilestoneStatus::Completed],
                    ['title' => 'Handover', 'description' => 'Snag complete, client walk-through.', 'status' => MilestoneStatus::Completed],
                ],
                'stats' => [
                    ['label' => 'Courtyard area', 'value' => '420 sqm'],
                    ['label' => 'Programme', 'value' => '10 weeks'],
                ],
                'before_after' => [
                    [
                        'before' => 'site_prep_ballast',
                        'after' => 'commercial_courtyard',
                        'caption' => 'From ballast platform to finished courtyard',
                        'description' => 'Same plot — platform first, then pavers and stone cladding.',
                    ],
                ],
            ]),
            $this->entry($config['site-prep-ballast'], [
                'excerpt' => 'Crushed ballast delivered and compacted for a residential foundation platform.',
                'body' => 'Site preparation for a Nairobi residential build: strip, fill, compact, and certify levels before the structure crew arrived.',
                'case_study' => 'Ballast was sourced locally, laid in tested layers, and compacted to a platform the structural team could trust — no surprises at pour.',
                'stage' => ConstructionStage::SitePreparation,
                'services' => ['site-preparation', 'construction-management'],
                'gallery' => ['site_prep_ballast'],
                'milestones' => [
                    ['title' => 'Strip & set-out', 'description' => 'Boundaries protected, levels established.', 'status' => MilestoneStatus::Completed],
                    ['title' => 'Ballast delivery', 'description' => 'Crushed stone placed in lifts.', 'status' => MilestoneStatus::InProgress],
                    ['title' => 'Compaction & handoff', 'description' => 'Platform ready for foundations.', 'status' => MilestoneStatus::Pending],
                ],
                'stats' => [['label' => 'Platform depth', 'value' => '450 mm']],
            ]),
            $this->entry($config['paving-gravel-leveling'], [
                'excerpt' => 'Gravel leveling beside interlocking brick paving on a suburban Nairobi plot.',
                'body' => 'Hardscaping package: edge restraints, compacted base, and paver laying with falls away from the building.',
                'case_study' => 'The critical work is what you do not see — base thickness, compaction, and drainage. The brick pattern is the easy part once the platform is right.',
                'stage' => ConstructionStage::Finishes,
                'services' => ['paving-hardscaping'],
                'gallery' => ['paving_gravel_leveling', 'commercial_courtyard'],
                'milestones' => [
                    ['title' => 'Base & edges', 'description' => 'Gravel placed and compacted.', 'status' => MilestoneStatus::Completed],
                    ['title' => 'Paver laying', 'description' => 'Pattern set and cut-ins complete.', 'status' => MilestoneStatus::InProgress],
                ],
            ]),
            $this->entry($config['structural-walkway'], [
                'excerpt' => 'Steel pergola frame over a tiled walkway during an active Kenyan construction build.',
                'body' => 'Structural steel pergola with tiled walkway — designed, fabricated, and erected by one accountable site team.',
                'case_study' => 'The walkway ties the main house to the garden room. Steel went up first, then roof sheeting, then tile finishes — all under one programme.',
                'stage' => ConstructionStage::Structure,
                'services' => ['construction-management', 'property-sketching'],
                'gallery' => ['structural_walkway', 'commercial_courtyard'],
                'milestones' => [
                    ['title' => 'Steel erection', 'description' => 'Frame bolted and aligned.', 'status' => MilestoneStatus::Completed],
                    ['title' => 'Roof & tiles', 'description' => 'Cover and walkway finishes underway.', 'status' => MilestoneStatus::InProgress],
                ],
                'stats' => [['label' => 'Span', 'value' => '14 m']],
            ]),
            $this->entry($config['courtyard-house'], [
                'excerpt' => 'Residential exterior finish package with courtyard paving and stone cladding.',
                'body' => 'Exterior envelope and courtyard for a Nairobi residence — one crew from scaffold to final clean.',
                'case_study' => 'The owner wanted the exterior to read as one composition. We matched stone tones to the paver palette before a single bag of cement was opened.',
                'stage' => ConstructionStage::Completed,
                'completion_year' => 2024,
                'services' => ['exterior-design', 'interior-design'],
                'gallery' => ['commercial_courtyard'],
                'milestones' => [
                    ['title' => 'Envelope', 'description' => 'Cladding and openings sealed.', 'status' => MilestoneStatus::Completed],
                    ['title' => 'Courtyard', 'description' => 'Paving and planting complete.', 'status' => MilestoneStatus::Completed],
                ],
            ]),
            $this->entry($config['garden-walkway'], [
                'excerpt' => 'Garden walkway and pergola structure in planning for a Kiambu residence.',
                'body' => 'Concept and programme for a landscaped walkway linking the house to a garden room — sketch approved, mobilisation next.',
                'case_study' => 'We start with how the family actually walks the plot. The pergola height and walkway width were set from site walks, not from a generic detail.',
                'stage' => ConstructionStage::Planning,
                'services' => ['property-sketching', 'plan-estimation'],
                'gallery' => ['structural_walkway'],
                'milestones' => [
                    ['title' => 'Concept sketch', 'description' => 'Layout approved by client.', 'status' => MilestoneStatus::Completed],
                    ['title' => 'BOQ & programme', 'description' => 'Estimate in progress.', 'status' => MilestoneStatus::InProgress],
                ],
            ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function entry(array $config, array $extra): array
    {
        $type = ProjectType::from($config['type']);
        $stage = $extra['stage'] instanceof ConstructionStage
            ? $extra['stage']
            : ConstructionStage::from($config['stage']);

        return [
            'title' => $config['title'],
            'slug' => $config['slug'],
            'category' => $config['category'],
            'type' => $type,
            'image' => $config['image'],
            'featured' => (bool) $config['featured'],
            'progress' => (int) $config['progress'],
            'county' => $config['county'],
            'city' => $config['city'],
            'completion_year' => $config['completion_year'] ?? null,
            'stage' => $stage,
            ...$extra,
        ];
    }
}
