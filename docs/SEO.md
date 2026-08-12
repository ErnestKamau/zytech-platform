# SEO & Discoverability

> Phase 15 reference — sitemap, robots, redirects, Open Graph, SEO scoring foundation.

## Public endpoints

- `/sitemap.xml` — cached URL set for home, about, services, projects, knowledge, quote, contact, search + published entities
- `/robots.txt` — allows public site; disallows admin/portal/account/ops tools

## Layout metadata

`layouts/website` emits canonical, Open Graph, and Twitter card tags from configuration SEO defaults. Pages may override via `@section` / `@stack('structured_data')`.

## Admin

Filament **Configuration → SEO redirects** manages `seo_redirects`. Middleware `HandleSeoRedirects` applies active redirects on GET requests.

## Scoring

`SeoScoreService` stores heuristic scores (0–100) on `seo_metadata` as an AI-SEO foundation (title/description/content length rules). Full AI audit is deferred.

## Cache

Sitemap XML is Redis-cached for one hour and cleared when services, projects, or articles change.
