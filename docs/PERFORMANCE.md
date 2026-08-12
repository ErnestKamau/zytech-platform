# Performance

> Phase 16 pragmatic layer — cache headers, Redis domain caches, Horizon queues.

## Already in place

- Redis for cache, sessions, queues
- Laravel Horizon (`/horizon`)
- Domain Redis caches (configuration, company, services, projects, knowledge, clients, portal dashboard, communication templates, search, sitemap)
- Queued listeners on mail / notifications / broadcast / search queues

## Added in this phase

- `AddPublicCacheHeaders` middleware — `Cache-Control: public, max-age=60` for anonymous successful GETs on public pages
- Security headers: `X-Content-Type-Options`, `Referrer-Policy`
- Sitemap / robots responses ship with explicit cache headers
- Search result caching (5 minutes)

## Ops tips

- Run `composer run dev` to start server + Horizon + Reverb + Vite together
- Keep `QUEUE_CONNECTION=redis` and a worker/Horizon process for Resend mail delivery
- Prefer `MAIL_MAILER=log` locally when Resend is not configured

## Deferred

- Full-page response cache / CDN edge rules
- Image CDN / advanced responsive media pipeline beyond Media domain stubs
- Query-level APM dashboards beyond Pulse/Telescope
