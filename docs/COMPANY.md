# Company

> Phase 4 reference for the Zytech Contractors Platform.

## Purpose

The Company domain is the source of truth for corporate information on the public site and in Filament. Views must not hardcode company copy, contact details, statistics, or people.

Photo and document uploads wait for Phase 5 Media. Until then, optional `photo_url`, `logo_url`, and `document_url` fields store URLs.

## Domain

`App\Domains\Company` owns:

- Singleton company profile (mission, vision, history, contact)
- Branches and leadership
- Certifications, awards, partners
- Testimonials, FAQs, homepage statistics

Canonical models live in `App\Models`: `Company`, `Branch`, `LeadershipMember`, `Certification`, `Award`, `Partner`, `Testimonial`, `Faq`, `CompanyStatistic`.

There is one company record. Filament hides create once it exists.

## Admin

Filament **Company** group:

- Company profile
- Branches
- Leadership
- Partners
- Certifications
- Awards
- Testimonials
- FAQs
- Statistics

Permissions: `company.view` (staff) and `company.update` (administrators). Super-admins bypass policies.

## Cache

`ApplicationCache` keys (prefix `zytech.`):

- `company.profile`
- `company.leadership`
- `company.branches`
- `company.testimonials`
- `company.partners`
- `company.statistics`
- `company.certifications`
- `company.faqs`
- `company.awards`

Updates invalidate only these keys — never the entire store.

## Public site

`ShareCompany` injects `$companyProfile` and `$companyStatistics` into the website layout, home, about, and contact pages.

- `/about` — Livewire `website.about-page`
- Home stats band — visible statistics
- Contact and footer — company email, phone, location (falls back to Configuration contact)

Only a **published** profile is shown publicly.

## Seed

`CompanySeeder` loads the Zytech profile, sample branches, leadership, stats, testimonials, FAQs, certifications, awards, partners, and an About item in header/footer navigation.
