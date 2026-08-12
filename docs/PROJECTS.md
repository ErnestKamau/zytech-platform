# Projects

> Phase 7 reference for the Zytech Contractors Platform.

## Purpose

The Projects domain is the source of truth for the public portfolio. Listing, detail, featured work, timelines, galleries, and service links read published records — not hardcoded cards.

Public photos and videos stay in `public/media/zyntech` and continue to be referenced from `config/zyntech-media.php` via `image_key`, gallery items, and `video_key`. Those files are not moved or replaced by Spatie Media Library URLs.

`config/zyntech-projects.php` remains the seed source for titles, slugs, categories, and image keys.

## Domain

`App\Domains\Project` owns:

- Project catalogue (status, visibility, featured, location, progress, SEO)
- Categories
- Gallery items, milestones, progress updates, statistics
- Before/after comparisons
- Related services (pivot to Services domain)

Canonical models live in `App\Models`: `Project`, `ProjectCategory`, `ProjectGalleryItem`, `ProjectMilestone`, `ProjectProgressUpdate`, `ProjectStatistic`, `ProjectBeforeAfter`.

Only **published** + **public** projects appear on the website.

## Admin

Filament **Projects** group:

- Categories
- Projects (gallery, milestones, statistics, before/after, progress updates, related services)

Permissions: `projects.view` (staff) and `projects.manage` (administrators). Super-admins bypass policies.

Cover images and gallery entries use keys from `config/zyntech-media.php`. Do not delete or rewire those files.

## Cache

`ApplicationCache` keys (prefix `zytech.`):

- `projects.published`
- `projects.featured`
- `projects.categories`
- `projects.map`
- `projects.show.{slug}`

Updates invalidate these keys — never the entire store.

## Public site

`ShareProjects` injects `$publishedProjects` and `$featuredProjects` into the homepage and projects listing.

- `/projects` — Livewire `website.projects-page`
- `/projects/category/{slug}` — filtered listing
- `/projects/{slug}` — Livewire `website.project-show`

Reusable Livewire: `project.featured-projects`, `project.related-projects`.

Service detail pages link to real projects through the `project_service` pivot when seeded.

Location markers use stored latitude/longitude. The listing shows a location grid; a full interactive Kenya map is a later enhancement.

## SEO

`ProjectSEOService` fills empty `meta_title`, `meta_description`, and `og_image_key` on create/update (queued). Detail pages use those fields for `<title>` and meta description.

## Seed

`ProjectSeeder` loads six categories, six portfolio projects from the current public site, milestones, gallery items, statistics, one before/after comparison, and service links.
