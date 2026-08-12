# Knowledge Centre

> Phase 8 reference for the Zytech Contractors Platform.

## Purpose

The Knowledge domain is the source of truth for educational articles on the public site. Listing, detail, featured guides, related services/projects, FAQs, and downloads read published records — not hardcoded content.

Public photos stay in `public/media/zyntech` and continue to be referenced from `config/zyntech-media.php` via `image_key`. Those files are not moved or replaced by Spatie Media Library URLs.

`config/zyntech-knowledge.php` remains the seed catalogue for titles, slugs, categories, and relationships.

## Domain

`App\Domains\Knowledge` owns:

- Article catalogue (status, visibility, featured, reading level/time, SEO)
- Categories, tags, authors
- Sections, FAQs, downloads
- Related services and projects (pivots)

Canonical models live in `App\Models`: `Article`, `ArticleCategory`, `ArticleTag`, `ArticleAuthor`, `ArticleSection`, `ArticleFaq`, `ArticleDownload`.

Only **published** + **public** articles appear on the website.

## Admin

Filament **Knowledge Centre** group:

- Categories
- Articles (sections, FAQs, downloads, tags, related services/projects)
- Authors
- Tags
- FAQs

Permissions: `knowledge.view` (staff) and `knowledge.manage` (administrators). Super-admins bypass policies.

Cover images use keys from `config/zyntech-media.php`. Do not delete or rewire those files.

## Cache

`ApplicationCache` keys (prefix `zytech.`):

- `knowledge.published`
- `knowledge.featured`
- `knowledge.categories`
- `knowledge.show.{slug}`

Updates invalidate these keys — never the entire store.

## Public site

`ShareKnowledge` injects `$publishedArticles` and `$featuredArticles` into the homepage and knowledge listing.

- `/knowledge` — Livewire `website.knowledge-page`
- `/knowledge/category/{slug}` — filtered listing
- `/knowledge/{slug}` — Livewire `website.article-show`

Reusable Livewire: `knowledge.featured-articles`, `knowledge.related-articles`, `knowledge.article-faqs`.

Service detail pages link to real articles through the `article_service` pivot when seeded.

Listing search filters title and excerpt via Livewire (`wire:model.live`).

## SEO

`ArticleSEOService` fills empty `meta_title`, `meta_description`, and `og_image_key` on create/update (queued). Detail pages use those fields for `<title>` and meta description.

## Seed

`KnowledgeSeeder` loads four categories, two authors, eight tags, six Kenyan construction guides with sections, FAQs, one download, and links to existing services and projects.

The `knowledge_centre` feature flag and primary navigation include Knowledge when seeded via `ConfigurationSeeder`.

## Deferred

- Newsletter integration
- AI SEO and structured data generation beyond basic meta fields
- Article analytics dashboard
- Full PostgreSQL full-text search index (basic ILIKE search is in place)
- Tests (per project rules)
