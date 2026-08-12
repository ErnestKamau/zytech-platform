<?php

namespace Database\Seeders;

use App\Core\Enums\ArticleStatus;
use App\Core\Enums\ArticleType;
use App\Core\Enums\ReadingLevel;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Domains\Knowledge\Services\ReadingTimeService;
use App\Models\Article;
use App\Models\ArticleAuthor;
use App\Models\ArticleCategory;
use App\Models\ArticleDownload;
use App\Models\ArticleFaq;
use App\Models\ArticleSection;
use App\Models\ArticleTag;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class KnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $categories = $this->seedCategories();
        $authors = $this->seedAuthors();
        $tags = $this->seedTags();
        $services = Service::query()->pluck('id', 'slug');
        $projects = Project::query()->pluck('id', 'slug');

        foreach ($this->catalogue() as $index => $row) {
            $article = Article::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'article_category_id' => $categories[$row['category']]->id,
                    'article_author_id' => $authors[$row['author']]->id,
                    'title' => $row['title'],
                    'excerpt' => $row['excerpt'],
                    'type' => $row['type'],
                    'status' => ArticleStatus::Published,
                    'visibility' => VisibilityStatus::Public,
                    'reading_level' => $row['reading_level'],
                    'reading_time_minutes' => $row['reading_time_minutes'],
                    'image_key' => $row['image'],
                    'is_featured' => $row['featured'],
                    'meta_title' => $row['title'].' — Zytech Contractors',
                    'meta_description' => $row['excerpt'],
                    'og_image_key' => $row['image'],
                    'published_at' => now()->subDays(30 - $index),
                    'sort_order' => $index,
                ],
            );

            $this->syncSections($article, $row['sections']);
            $this->syncFaqs($article, $row['faqs'] ?? []);
            $this->syncDownloads($article, $row['downloads'] ?? []);
            $this->syncTags($article, $row['tags'] ?? [], $tags);
            $this->syncServices($article, $row['services'] ?? [], $services);
            $this->syncProjects($article, $row['projects'] ?? [], $projects);
        }

        app(KnowledgeCentreService::class)->forget();
    }

    /**
     * @return array<string, ArticleCategory>
     */
    private function seedCategories(): array
    {
        $rows = [
            ['name' => 'Site works', 'slug' => 'site-works', 'description' => 'Groundworks, ballast, compaction, and platform preparation.', 'sort_order' => 0],
            ['name' => 'Regulations', 'slug' => 'regulations', 'description' => 'NCA, county approvals, and statutory compliance.', 'sort_order' => 1],
            ['name' => 'Materials & finishes', 'slug' => 'materials', 'description' => 'Pavers, stone, steel, and exterior finishes.', 'sort_order' => 2],
            ['name' => 'Cost & planning', 'slug' => 'cost-planning', 'description' => 'BOQs, budgets, and estimation best practice.', 'sort_order' => 3],
        ];

        $categories = [];

        foreach ($rows as $row) {
            $categories[$row['slug']] = ArticleCategory::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [...$row, 'is_published' => true],
            );
        }

        return $categories;
    }

    /**
     * @return array<string, ArticleAuthor>
     */
    private function seedAuthors(): array
    {
        $rows = [
            [
                'name' => 'Zytech Editorial Team',
                'slug' => 'zytech-editorial',
                'role' => 'Knowledge Centre',
                'bio' => 'Field notes and guides compiled from active Zytech projects across Nairobi and Kiambu.',
                'photo_key' => null,
                'sort_order' => 0,
            ],
            [
                'name' => 'Site Engineering Desk',
                'slug' => 'site-engineering-desk',
                'role' => 'Technical review',
                'bio' => 'Site engineers who review structural, paving, and preparation guidance before publication.',
                'photo_key' => null,
                'sort_order' => 1,
            ],
        ];

        $authors = [];

        foreach ($rows as $row) {
            $authors[$row['slug']] = ArticleAuthor::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [...$row, 'is_visible' => true],
            );
        }

        return $authors;
    }

    /**
     * @return array<string, ArticleTag>
     */
    private function seedTags(): array
    {
        $names = [
            'Nairobi',
            'Kiambu',
            'Approvals',
            'Ballast',
            'Pavers',
            'Budget',
            'Structural',
            'Homeowners',
        ];

        $tags = [];

        foreach ($names as $name) {
            $tags[str()->slug($name)] = ArticleTag::query()->updateOrCreate(['name' => $name]);
        }

        return $tags;
    }

    /**
     * @param  list<array{heading?: string, body: string, image?: ?string}>  $sections
     */
    private function syncSections(Article $article, array $sections): void
    {
        foreach ($sections as $order => $section) {
            ArticleSection::query()->updateOrCreate(
                [
                    'article_id' => $article->id,
                    'sort_order' => $order,
                ],
                [
                    'heading' => $section['heading'] ?? null,
                    'body' => $section['body'],
                    'image_key' => $section['image'] ?? null,
                ],
            );
        }
    }

    /**
     * @param  list<array{question: string, answer: string}>  $faqs
     */
    private function syncFaqs(Article $article, array $faqs): void
    {
        foreach ($faqs as $order => $faq) {
            ArticleFaq::query()->updateOrCreate(
                ['article_id' => $article->id, 'question' => $faq['question']],
                ['answer' => $faq['answer'], 'is_published' => true, 'sort_order' => $order],
            );
        }
    }

    /**
     * @param  list<array{title: string, description?: string, external_url?: string}>  $downloads
     */
    private function syncDownloads(Article $article, array $downloads): void
    {
        foreach ($downloads as $order => $download) {
            ArticleDownload::query()->updateOrCreate(
                ['article_id' => $article->id, 'title' => $download['title']],
                [
                    'description' => $download['description'] ?? null,
                    'external_url' => $download['external_url'] ?? null,
                    'sort_order' => $order,
                ],
            );
        }
    }

    /**
     * @param  list<string>  $tagSlugs
     * @param  array<string, ArticleTag>  $tags
     */
    private function syncTags(Article $article, array $tagSlugs, array $tags): void
    {
        $ids = collect($tagSlugs)
            ->map(fn (string $slug): ?string => $tags[$slug]?->id ?? null)
            ->filter()
            ->values()
            ->all();

        if ($ids !== []) {
            $article->tags()->sync($ids);
        }
    }

    /**
     * @param  Collection<string, string>  $services
     */
    private function syncServices(Article $article, array $serviceSlugs, $services): void
    {
        $ids = collect($serviceSlugs)
            ->map(fn (string $slug): ?string => $services[$slug] ?? null)
            ->filter()
            ->values()
            ->all();

        if ($ids !== []) {
            $article->services()->sync(
                collect($ids)->mapWithKeys(fn (string $id, int $order): array => [$id => ['sort_order' => $order]])->all(),
            );
        }
    }

    /**
     * @param  Collection<string, string>  $projects
     */
    private function syncProjects(Article $article, array $projectSlugs, $projects): void
    {
        $ids = collect($projectSlugs)
            ->map(fn (string $slug): ?string => $projects[$slug] ?? null)
            ->filter()
            ->values()
            ->all();

        if ($ids !== []) {
            $article->projects()->sync(
                collect($ids)->mapWithKeys(fn (string $id, int $order): array => [$id => ['sort_order' => $order]])->all(),
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function catalogue(): array
    {
        $readingTime = app(ReadingTimeService::class);

        $articles = [
            [
                'title' => 'Preparing a Kenyan residential site for construction',
                'slug' => 'preparing-residential-site',
                'category' => 'site-works',
                'author' => 'zytech-editorial',
                'type' => ArticleType::Guide,
                'featured' => true,
                'image' => 'site_prep_ballast',
                'reading_level' => ReadingLevel::Beginner,
                'excerpt' => 'From access roads to platform levels — what must happen before the slab is poured on a Nairobi or Kiambu plot.',
                'tags' => ['nairobi', 'ballast', 'homeowners'],
                'services' => ['site-preparation', 'construction-management'],
                'projects' => ['site-prep-ballast'],
                'sections' => [
                    [
                        'heading' => 'Start with access and drainage',
                        'body' => 'Before trucks arrive, confirm the plot access can take a tipper load without damaging neighbouring walls. Surface water must drain away from the future building line — especially on red-soil plots in Karen, Ruaka, and Ruiru where seasonal runoff is heavy.',
                        'image' => 'site_prep_ballast',
                    ],
                    [
                        'heading' => 'Platform levels and compaction',
                        'body' => 'A residential platform is not “flat enough by eye.” Crews should work to a surveyed reduced level, importing hardcore or ballast in lifts and compacting each layer. Skipping compaction leads to differential settlement that shows up years later in cracked paving and door frames that stick.',
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'How long should site preparation take?',
                        'answer' => 'For a typical Nairobi townhouse plot, one to two weeks is common once approvals and material deliveries are aligned.',
                    ],
                ],
            ],
            [
                'title' => 'NCA and county building approvals in Kenya',
                'slug' => 'nca-county-approvals',
                'category' => 'regulations',
                'author' => 'site-engineering-desk',
                'type' => ArticleType::Regulation,
                'featured' => true,
                'image' => 'commercial_courtyard',
                'reading_level' => ReadingLevel::Intermediate,
                'excerpt' => 'A practical map of NCA registration, county plan submission, and the documents owners need before breaking ground.',
                'tags' => ['approvals', 'nairobi', 'homeowners'],
                'services' => ['plan-approvals', 'property-sketching'],
                'projects' => ['commercial-courtyard'],
                'sections' => [
                    [
                        'heading' => 'Align drawings before submission',
                        'body' => 'County reviewers compare architectural, structural, and service drawings for consistency. Mismatched set-backs or structural grid lines are the most common reason for rejection — not the fee payment itself.',
                    ],
                    [
                        'heading' => 'Keep NCA and county timelines separate',
                        'body' => 'Contractor registration with NCA and county plan approval are related but distinct. Homeowners should confirm both are in place before mobilising plant on site.',
                    ],
                ],
            ],
            [
                'title' => 'Interlocking pavers vs natural stone for Nairobi courtyards',
                'slug' => 'pavers-vs-stone-courtyards',
                'category' => 'materials',
                'author' => 'zytech-editorial',
                'type' => ArticleType::Material,
                'featured' => false,
                'image' => 'paving_gravel_leveling',
                'reading_level' => ReadingLevel::Beginner,
                'excerpt' => 'Compare cost, drainage, maintenance, and heat performance for commercial courtyards and residential patios.',
                'tags' => ['pavers', 'nairobi'],
                'services' => ['paving-hardscaping', 'exterior-design'],
                'projects' => ['paving-gravel-leveling', 'commercial-courtyard'],
                'sections' => [
                    [
                        'heading' => 'Pavers suit flexible layouts',
                        'body' => 'Interlocking pavers handle minor ground movement and are faster to repair in sections. They work well around service covers and curved courtyard edges.',
                        'image' => 'paving_gravel_leveling',
                    ],
                    [
                        'heading' => 'Stone delivers a premium finish',
                        'body' => 'Natural stone elevates commercial entrances and hospitality courtyards. Budget for stronger sub-base design and experienced fixing teams — the material cost is only part of the story.',
                    ],
                ],
            ],
            [
                'title' => 'Ballast delivery and compaction: what homeowners should know',
                'slug' => 'ballast-delivery-compaction',
                'category' => 'site-works',
                'author' => 'site-engineering-desk',
                'type' => ArticleType::Guide,
                'featured' => false,
                'image' => 'site_prep_ballast',
                'reading_level' => ReadingLevel::Beginner,
                'excerpt' => 'How to verify quantities, spot poor compaction, and coordinate deliveries on tight urban plots.',
                'tags' => ['ballast', 'homeowners', 'kiambu'],
                'services' => ['site-preparation'],
                'projects' => ['site-prep-ballast'],
                'sections' => [
                    [
                        'heading' => 'Verify volume, not just truck count',
                        'body' => 'Ask for calibrated quantities and keep a simple delivery log. On constrained plots in Westlands or Kilimani, schedule off-peak deliveries to avoid queuing on neighbouring access roads.',
                    ],
                ],
            ],
            [
                'title' => 'Steel pergola frames for covered walkways',
                'slug' => 'steel-pergola-walkways',
                'category' => 'materials',
                'author' => 'site-engineering-desk',
                'type' => ArticleType::Guide,
                'featured' => false,
                'image' => 'structural_walkway',
                'reading_level' => ReadingLevel::Intermediate,
                'excerpt' => 'Design and sequencing tips for pergola steel before cladding, roofing, and paving trades arrive.',
                'tags' => ['structural', 'nairobi'],
                'services' => ['construction-management', 'exterior-design'],
                'projects' => ['structural-walkway', 'garden-walkway'],
                'sections' => [
                    [
                        'heading' => 'Set out from finished paving levels',
                        'body' => 'Walkway steel should be set out relative to finished floor and paving levels — not the temporary site platform. That avoids steps and headroom clashes at handover.',
                        'image' => 'structural_walkway',
                    ],
                ],
            ],
            [
                'title' => 'BOQ basics: how plan estimation protects your budget',
                'slug' => 'boq-plan-estimation-budget',
                'category' => 'cost-planning',
                'author' => 'zytech-editorial',
                'type' => ArticleType::CostGuide,
                'featured' => true,
                'image' => 'commercial_courtyard',
                'reading_level' => ReadingLevel::Beginner,
                'excerpt' => 'Why a detailed BOQ beats lump-sum guessing for Kenyan residential and commercial builds.',
                'tags' => ['budget', 'homeowners'],
                'services' => ['plan-estimation', 'construction-management'],
                'projects' => ['courtyard-house'],
                'sections' => [
                    [
                        'heading' => 'Separate provisional sums from fixed items',
                        'body' => 'A good BOQ labels provisional quantities — earthworks, contingencies, client-selected finishes — so variation orders are predictable instead of adversarial.',
                    ],
                    [
                        'heading' => 'Tie estimates to drawings',
                        'body' => 'Each BOQ line should trace back to a drawing reference. When the design changes, the cost impact is visible immediately rather than discovered mid-build.',
                    ],
                ],
                'downloads' => [
                    [
                        'title' => 'Sample BOQ checklist',
                        'description' => 'Questions to ask before accepting a contractor quotation.',
                        'external_url' => 'https://zytech.co.ke/contact',
                    ],
                ],
            ],
        ];

        return array_map(function (array $article) use ($readingTime): array {
            $text = collect($article['sections'])->pluck('body')->implode(' ');
            $article['reading_time_minutes'] = $readingTime->fromText($text);

            return $article;
        }, $articles);
    }
}
