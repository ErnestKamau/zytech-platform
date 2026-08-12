# Services

> Phase 6 reference for the Zytech Contractors Platform.

## Purpose

The Services domain is the source of truth for the public service catalogue. Homepage cards, `/services`, category pages, and individual service pages read published records — not hardcoded copy.

Public photos and videos stay in `public/media/zyntech` and continue to be referenced from `config/zyntech-media.php` via `image_key`, `gallery_keys`, and `og_image_key`. Those files are not moved or replaced by Spatie Media Library URLs.

`config/zyntech-services.php` remains the seed source for titles, slugs, icons, and image keys.

## Domain

`App\Domains\Service` owns:

- Service catalogue (status, visibility, featured, pricing, SEO)
- Categories
- Features, process steps, per-service FAQs, statistics
- Related projects linked via the `project_service` pivot

Canonical models live in `App\Models`: `Service`, `ServiceCategory`, `ServiceFeature`, `ServiceProcess`, `ServiceFaq`, `ServiceStatistic`, `ServiceRelatedProject`.

Only **published** + **public** services appear on the website.

## Admin

Filament **Services** group:

- Categories
- Services (features, process, statistics, related project teasers, gallery keys)
- FAQs

Permissions: `services.view` (staff) and `services.manage` (administrators). Super-admins bypass policies.

Cover images are chosen from `config/zyntech-media.php` keys. Do not delete or rewire those files.

## Cache

`ApplicationCache` keys (prefix `zytech.`):

- `services.published`
- `services.featured`
- `services.categories`
- `services.homepage`
- `services.show.{slug}`

Updates invalidate these keys — never the entire store.

## Public site

`ShareServices` injects `$publishedServices` and `$featuredServices` into the homepage.

- `/services` — Livewire `website.services-page`
- `/services/category/{slug}` — same listing, filtered
- `/services/{slug}` — Livewire `website.service-show`

Reusable Livewire: `service.featured-services`, `service.related-services`, `service.faqs`.

Quotation CTA on listing and detail points at `/contact` until the Quotation domain.

Related projects link to `/projects/{slug}` when connected through the `project_service` pivot. Related articles wait for the Knowledge domain.

## SEO

`ServiceSEOService` fills empty `meta_title`, `meta_description`, and `og_image_key` on create/update (queued). Detail pages use those fields for `<title>` and meta description.

## Seed

`ServiceSeeder` loads four categories and the eight current public services, with features, process steps, FAQs, statistics, and related-project teasers.
