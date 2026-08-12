# Configuration

> Phase 3 reference for the Zytech Contractors Platform.

## Purpose

Administrators manage platform-wide settings from Filament instead of editing code or `.env` for public content.

## Domain

`App\Domains\Configuration` owns:

- System settings (grouped key/value)
- Branding and default SEO
- Contact and social links
- Feature flags
- Header / footer navigation

Canonical models live in `App\Models`: `SettingGroup`, `Setting`, `FeatureFlag`, `NavigationMenu`, `NavigationItem`.

## Admin

Filament **Configuration** group:

- Settings
- Feature flags
- Navigation

Only users with `settings.manage` / `navigation.manage` / `feature-flags.manage` may change records. Super-admins bypass policies.

## Cache

`ApplicationCache` keys (prefix `zytech.`):

- `settings.all`
- `settings.branding`
- `settings.seo`
- `settings.contact`
- `settings.social`
- `feature-flags.all`
- `navigation.{location}`

Updates invalidate only these keys — never the entire store.

## Public site

`ShareConfiguration` injects a `$platform` bag into the website layout, header, and footer.

Maintenance: enable the `maintenance_mode` feature flag. Visitors see HTTP 503; `/admin`, `/login`, and staff roles still work.

## Seed

`ConfigurationSeeder` loads default Zytech branding, contact, SEO, flags, and published header/footer menus.
