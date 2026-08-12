<?php

namespace Database\Seeders;

use App\Core\Enums\PricingModel;
use App\Core\Enums\ServiceStatus;
use App\Core\Enums\ServiceType;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Service\Services\ServiceService;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceFaq;
use App\Models\ServiceFeature;
use App\Models\ServiceProcess;
use App\Models\ServiceRelatedProject;
use App\Models\ServiceStatistic;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = $this->seedCategories();
        $this->seedServices($categories);

        app(ServiceService::class)->forget();
    }

    /**
     * @return array<string, ServiceCategory>
     */
    private function seedCategories(): array
    {
        $rows = [
            [
                'name' => 'Design',
                'slug' => 'design',
                'description' => 'Interiors, exteriors, and concept sketches that sell the vision.',
                'sort_order' => 0,
            ],
            [
                'name' => 'Planning',
                'slug' => 'planning',
                'description' => 'Estimates, BOQs, and statutory approvals before a stone is laid.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Construction',
                'slug' => 'construction',
                'description' => 'One accountable crew from groundbreaking to handover.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Site works',
                'slug' => 'site-works',
                'description' => 'Groundworks, paving, and hardscaping built for East African weather.',
                'sort_order' => 3,
            ],
        ];

        $categories = [];

        foreach ($rows as $row) {
            $categories[$row['slug']] = ServiceCategory::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    ...$row,
                    'is_published' => true,
                ],
            );
        }

        return $categories;
    }

    /**
     * @param  array<string, ServiceCategory>  $categories
     */
    private function seedServices(array $categories): void
    {
        foreach ($this->catalogue() as $index => $row) {
            $category = $categories[$row['category']];

            $service = Service::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'service_category_id' => $category->id,
                    'title' => $row['title'],
                    'excerpt' => $row['excerpt'],
                    'body' => $row['body'],
                    'icon_path' => $row['icon'],
                    'image_key' => $row['image'],
                    'gallery_keys' => $row['gallery'],
                    'type' => $row['type'],
                    'status' => ServiceStatus::Published,
                    'visibility' => VisibilityStatus::Public,
                    'pricing_model' => PricingModel::QuoteOnRequest,
                    'price_currency' => 'KES',
                    'pricing_notes' => 'Site visit and quotation on request.',
                    'is_featured' => $row['featured'],
                    'meta_title' => $row['title'].' — Zytech Contractors',
                    'meta_description' => $row['excerpt'],
                    'og_image_key' => $row['image'],
                    'published_at' => now(),
                    'sort_order' => $index,
                ],
            );

            $this->syncChildren($service, $row);
        }
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function syncChildren(Service $service, array $row): void
    {
        foreach ($row['features'] as $order => $feature) {
            ServiceFeature::query()->updateOrCreate(
                [
                    'service_id' => $service->id,
                    'title' => $feature['title'],
                ],
                [
                    'description' => $feature['description'],
                    'sort_order' => $order,
                ],
            );
        }

        foreach ($row['processes'] as $order => $process) {
            ServiceProcess::query()->updateOrCreate(
                [
                    'service_id' => $service->id,
                    'title' => $process['title'],
                ],
                [
                    'description' => $process['description'],
                    'sort_order' => $order,
                ],
            );
        }

        foreach ($row['faqs'] as $order => $faq) {
            ServiceFaq::query()->updateOrCreate(
                [
                    'service_id' => $service->id,
                    'question' => $faq['question'],
                ],
                [
                    'answer' => $faq['answer'],
                    'is_published' => true,
                    'sort_order' => $order,
                ],
            );
        }

        foreach ($row['stats'] as $order => $stat) {
            ServiceStatistic::query()->updateOrCreate(
                [
                    'service_id' => $service->id,
                    'label' => $stat['label'],
                ],
                [
                    'value' => $stat['value'],
                    'is_visible' => true,
                    'sort_order' => $order,
                ],
            );
        }

        foreach ($row['related'] as $order => $related) {
            ServiceRelatedProject::query()->updateOrCreate(
                [
                    'service_id' => $service->id,
                    'title' => $related['title'],
                ],
                [
                    'slug' => $related['slug'],
                    'summary' => $related['summary'],
                    'image_key' => $related['image'],
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
        $config = config('zyntech-services');
        $bySlug = collect($config)->keyBy('slug');

        return [
            $this->entry($bySlug['interior-design'], [
                'category' => 'design',
                'type' => ServiceType::Design,
                'gallery' => ['commercial_courtyard', 'structural_walkway'],
                'body' => 'We plan rooms that work for how Kenyan families and offices actually live — light, ventilation, circulation, and finishes that last. From first layout to site instruction, one team owns the interior package.',
                'features' => [
                    ['title' => 'Space planning', 'description' => 'Layouts that balance furniture, flow, and Kenyan climate.'],
                    ['title' => 'Material specification', 'description' => 'Finishes sourced for durability and local availability.'],
                    ['title' => 'Site coordination', 'description' => 'The same crew that draws the interior builds it.'],
                ],
                'processes' => [
                    ['title' => 'Brief', 'description' => 'Walk the rooms, capture how you use the space.'],
                    ['title' => 'Concept', 'description' => 'Mood, layout, and finish options you can decide on.'],
                    ['title' => 'Build', 'description' => 'Joinery, finishes, and snagging under one programme.'],
                ],
                'faqs' => [
                    ['question' => 'Do you work on occupied homes?', 'answer' => 'Yes. We sequence rooms so you can keep living on site where possible.'],
                    ['question' => 'Can you match existing finishes?', 'answer' => 'We sample locally first, then specify equivalents that will still be available at handover.'],
                ],
                'stats' => [
                    ['label' => 'Interior packages', 'value' => '40+'],
                    ['label' => 'Typical programme', 'value' => '6–12 weeks'],
                ],
                'related' => [
                    [
                        'title' => 'Commercial courtyard interiors',
                        'slug' => 'commercial-courtyard',
                        'summary' => 'Stone cladding and outdoor rooms that read as interior space.',
                        'image' => 'commercial_courtyard',
                    ],
                ],
            ]),
            $this->entry($bySlug['exterior-design'], [
                'category' => 'design',
                'type' => ServiceType::Design,
                'gallery' => ['commercial_courtyard', 'structural_walkway'],
                'body' => 'Facades and outdoor rooms engineered for Nairobi sun, dust, and rain. We design elevations, cladding, and landscape edges that still look right five years on.',
                'features' => [
                    ['title' => 'Climate-first facades', 'description' => 'Shading, runoff, and materials that survive East African weather.'],
                    ['title' => 'Street presence', 'description' => 'Elevations that read clearly from the gate and the road.'],
                    ['title' => 'Outdoor rooms', 'description' => 'Courtyards and terraces treated as proper living space.'],
                ],
                'processes' => [
                    ['title' => 'Survey', 'description' => 'Measure the plot, sun path, and neighbouring context.'],
                    ['title' => 'Elevation study', 'description' => 'Massing and cladding options before we commit on site.'],
                    ['title' => 'Execute', 'description' => 'Structure, finishes, and landscape edge in one sequence.'],
                ],
                'faqs' => [
                    ['question' => 'Do you handle county elevation comments?', 'answer' => 'Yes. Exterior packages are drawn so approvals and site work stay aligned.'],
                    ['question' => 'Can you redesign an existing facade?', 'answer' => 'We regularly reclad and recompose occupied buildings without a full teardown.'],
                ],
                'stats' => [
                    ['label' => 'Facade packages', 'value' => '25+'],
                    ['label' => 'Service area', 'value' => 'Nairobi & Kiambu'],
                ],
                'related' => [
                    [
                        'title' => 'Finished commercial courtyard',
                        'slug' => 'commercial-courtyard',
                        'summary' => 'Stone, pavers, and shade built as one exterior room.',
                        'image' => 'commercial_courtyard',
                    ],
                ],
            ]),
            $this->entry($bySlug['plan-estimation'], [
                'category' => 'planning',
                'type' => ServiceType::Planning,
                'gallery' => [],
                'body' => 'Accurate bills of quantities and budgets before a single stone is laid. You see labour, materials, and contingency in Kenyan shillings — not a round number that unravels on site.',
                'features' => [
                    ['title' => 'Measured BOQs', 'description' => 'Quantities taken off drawings, not guessed from similar jobs.'],
                    ['title' => 'Local rates', 'description' => 'Labour and materials priced for Nairobi and Kiambu markets.'],
                    ['title' => 'Contingency you can defend', 'description' => 'Risk called out so principals are not surprised mid-build.'],
                ],
                'processes' => [
                    ['title' => 'Drawings in', 'description' => 'We take off from your set or produce one with design.'],
                    ['title' => 'Price', 'description' => 'Itemised labour, plant, and materials.'],
                    ['title' => 'Walkthrough', 'description' => 'We sit with you and explain every allowance.'],
                ],
                'faqs' => [
                    ['question' => 'Is the estimate free?', 'answer' => 'A first-pass budget can sit inside a quotation request. Full BOQs are scoped as a paid package.'],
                    ['question' => 'Do you estimate other contractors’ drawings?', 'answer' => 'Yes. We price what is on paper and flag gaps before you tender.'],
                ],
                'stats' => [
                    ['label' => 'BOQs issued', 'value' => '80+'],
                    ['label' => 'Typical turnaround', 'value' => '5–10 days'],
                ],
                'related' => [],
            ]),
            $this->entry($bySlug['property-sketching'], [
                'category' => 'design',
                'type' => ServiceType::Design,
                'gallery' => ['structural_walkway'],
                'body' => 'Concept development and property sketches that sell the vision to family, partners, or a bank — before you spend on a full working set.',
                'features' => [
                    ['title' => 'Fast concepts', 'description' => 'Sketches you can share in a week, not a quarter.'],
                    ['title' => 'Plot-aware', 'description' => 'We draw to your actual boundaries, slope, and access.'],
                    ['title' => 'Handoff to build', 'description' => 'Approved sketches become the working set without starting over.'],
                ],
                'processes' => [
                    ['title' => 'Visit', 'description' => 'Walk the plot and capture constraints.'],
                    ['title' => 'Sketch', 'description' => 'Plan and elevation options you can choose from.'],
                    ['title' => 'Refine', 'description' => 'Lock the preferred option for estimation and approvals.'],
                ],
                'faqs' => [
                    ['question' => 'Are sketches enough for county approval?', 'answer' => 'No. Sketches sell the idea. We then produce the statutory set under Plan Approvals.'],
                    ['question' => 'Can you sketch from a photo of the plot?', 'answer' => 'We still visit. Photos miss slope, access, and neighbour setbacks.'],
                ],
                'stats' => [
                    ['label' => 'Concept packs', 'value' => '60+'],
                    ['label' => 'First sketches', 'value' => '7 days'],
                ],
                'related' => [
                    [
                        'title' => 'Structural walkway concept to build',
                        'slug' => 'structural-walkway',
                        'summary' => 'Steel frame and tiled walkway that started as a sketch.',
                        'image' => 'structural_walkway',
                    ],
                ],
            ]),
            $this->entry($bySlug['plan-approvals'], [
                'category' => 'planning',
                'type' => ServiceType::Planning,
                'gallery' => [],
                'body' => 'Statutory approvals guided end-to-end, without the runaround. We prepare the set, lodge it, and stay on the comments until you have a stamped permit.',
                'features' => [
                    ['title' => 'Complete sets', 'description' => 'Architectural, structural, and drainage drawings that match.'],
                    ['title' => 'County liaison', 'description' => 'We own the comments loop so you are not chasing stamps.'],
                    ['title' => 'Build-ready', 'description' => 'Approved drawings are the same ones the site crew uses.'],
                ],
                'processes' => [
                    ['title' => 'Audit', 'description' => 'Check what you already have against county requirements.'],
                    ['title' => 'Lodge', 'description' => 'Submit a coordinated set.'],
                    ['title' => 'Clear', 'description' => 'Respond to comments until the permit is in hand.'],
                ],
                'faqs' => [
                    ['question' => 'Which counties do you cover?', 'answer' => 'Nairobi and Kiambu routinely, and other counties by arrangement.'],
                    ['question' => 'How long do approvals take?', 'answer' => 'It depends on the authority. We give a realistic window after we see the current set.'],
                ],
                'stats' => [
                    ['label' => 'Permits guided', 'value' => '50+'],
                    ['label' => 'Counties', 'value' => 'Nairobi · Kiambu'],
                ],
                'related' => [],
            ]),
            $this->entry($bySlug['construction-management'], [
                'category' => 'construction',
                'type' => ServiceType::Construction,
                'gallery' => ['structural_walkway', 'site_prep_ballast'],
                'body' => 'One accountable team from groundbreaking to handover. Programme, materials, subcontractors, and snagging sit with the same people you met at the estimate.',
                'features' => [
                    ['title' => 'Single point of contact', 'description' => 'You speak to the people who will still be there at handover.'],
                    ['title' => 'Programme control', 'description' => 'Weekly site reports against a published programme.'],
                    ['title' => 'Quality on the tools', 'description' => 'Supervisors who read the drawings and walk the pours.'],
                ],
                'processes' => [
                    ['title' => 'Mobilise', 'description' => 'Site setup, welfare, and first deliveries.'],
                    ['title' => 'Build', 'description' => 'Structure, envelope, and finishes to programme.'],
                    ['title' => 'Handover', 'description' => 'Snag, certify, and leave you with as-builts.'],
                ],
                'faqs' => [
                    ['question' => 'Do you take over a stalled site?', 'answer' => 'Yes, after a condition survey and a reset programme.'],
                    ['question' => 'Can we keep our own electrician?', 'answer' => 'Specialist subcontractors can sit under our programme if they meet site rules.'],
                ],
                'stats' => [
                    ['label' => 'Builds managed', 'value' => '120+'],
                    ['label' => 'On-time completion', 'value' => '96%'],
                ],
                'related' => [
                    [
                        'title' => 'Active structural walkway',
                        'slug' => 'structural-walkway',
                        'summary' => 'Steel pergola and tiled walkway under one site team.',
                        'image' => 'structural_walkway',
                    ],
                ],
            ]),
            $this->entry($bySlug['site-preparation'], [
                'category' => 'site-works',
                'type' => ServiceType::SiteWorks,
                'gallery' => ['site_prep_ballast', 'paving_gravel_leveling'],
                'body' => 'Groundworks, ballast, and leveling — the foundation every Kenyan build starts on. We clear, cut, fill, and compact so structure does not inherit a bad platform.',
                'features' => [
                    ['title' => 'Platform first', 'description' => 'Levels and compaction checked before anyone pours.'],
                    ['title' => 'Local materials', 'description' => 'Ballast and murram sourced for the job, not leftover stock.'],
                    ['title' => 'Drainage from day one', 'description' => 'We do not trap water under the slab.'],
                ],
                'processes' => [
                    ['title' => 'Clear', 'description' => 'Strip, set out, and protect boundaries.'],
                    ['title' => 'Fill & compact', 'description' => 'Ballast, murram, and tested layers.'],
                    ['title' => 'Handoff', 'description' => 'A platform the structure crew can trust.'],
                ],
                'faqs' => [
                    ['question' => 'Do you handle rock excavation?', 'answer' => 'Yes, with the right plant. We price it after a site look, not from a photo.'],
                    ['question' => 'Can you prep for another contractor?', 'answer' => 'We can deliver a platform and levels certificate for a follow-on crew.'],
                ],
                'stats' => [
                    ['label' => 'Sites prepared', 'value' => '90+'],
                    ['label' => 'Typical duration', 'value' => '1–3 weeks'],
                ],
                'related' => [
                    [
                        'title' => 'Ballast delivery and platform',
                        'slug' => 'site-prep-ballast',
                        'summary' => 'Crushed ballast on a residential plot ready for foundations.',
                        'image' => 'site_prep_ballast',
                    ],
                ],
            ]),
            $this->entry($bySlug['paving-hardscaping'], [
                'category' => 'site-works',
                'type' => ServiceType::SiteWorks,
                'gallery' => ['paving_gravel_leveling', 'commercial_courtyard'],
                'body' => 'Interlocking pavers, courtyards, and outdoor rooms built for East African weather. Base, edge restraints, and falls are as important as the brick you see.',
                'features' => [
                    ['title' => 'Proper base', 'description' => 'Compacted layers so pavers do not dish in the rains.'],
                    ['title' => 'Courtyard rooms', 'description' => 'Paving designed with seating, shade, and drainage.'],
                    ['title' => 'Match to building', 'description' => 'Colours and patterns that sit with the facade, not against it.'],
                ],
                'processes' => [
                    ['title' => 'Set out', 'description' => 'Falls, edges, and levels locked to the building.'],
                    ['title' => 'Base', 'description' => 'Gravel, compaction, and edge restraints.'],
                    ['title' => 'Lay', 'description' => 'Pavers, jointing, and a clean handover.'],
                ],
                'faqs' => [
                    ['question' => 'Do you supply the pavers?', 'answer' => 'We can supply or lay client-bought stock after we check quality and calibre.'],
                    ['question' => 'Will it hold in heavy rains?', 'answer' => 'If the base and falls are right. That is the part we refuse to skip.'],
                ],
                'stats' => [
                    ['label' => 'Hardscape jobs', 'value' => '35+'],
                    ['label' => 'Typical courtyard', 'value' => '2–4 weeks'],
                ],
                'related' => [
                    [
                        'title' => 'Paving and gravel leveling',
                        'slug' => 'paving-gravel-leveling',
                        'summary' => 'Crew leveling crushed stone beside interlocking brick.',
                        'image' => 'paving_gravel_leveling',
                    ],
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
        return [
            'title' => $config['title'],
            'slug' => $config['slug'],
            'excerpt' => $config['body'],
            'icon' => $config['icon'],
            'image' => $config['image'],
            'featured' => (bool) $config['featured'],
            ...$extra,
        ];
    }
}
