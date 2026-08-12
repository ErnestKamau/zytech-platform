# Public Website

> Phase 12 reference — marketing site surfaces, legal pages, and downloads.

## Stack

- Blade layouts (`layouts.website`) + handcrafted Vite CSS (`resources/css/website`)
- Livewire pages registered in `AppServiceProvider` (`website.*` components)
- View composers: `ShareConfiguration`, `ShareCompany`, plus domain shares for services / projects / knowledge

## Routes (public)

| Path | Name | Notes |
|------|------|--------|
| `/` | `home` | Testimonials + FAQs when published data exists |
| `/about` | — | Company story |
| `/services`, `/projects`, `/knowledge` | — | Catalogue + detail |
| `/contact`, `/quote`, `/quote/track` | — | Lead capture |
| `/search` | `search` | Global search UI |
| `/downloads` | `downloads.index` | Public article downloads |
| `/privacy` | `privacy` | Privacy policy |
| `/terms` | `terms` | Terms & conditions |
| `/careers` | `careers` | Future stub + contact CTA |

Custom `errors/404` uses the website layout.

## Accessibility (baseline)

See [ACCESSIBILITY.md](./ACCESSIBILITY.md).

## Performance targets

See [PERFORMANCE.md](./PERFORMANCE.md).

## Realtime toasts

Authenticated visitors receive Reverb toasts via `resources/js/app.js` (`NotificationPushed` on the private user channel and public `platform.announcements`). Host markup lives in `layouts.website` / `layouts.portal`.
