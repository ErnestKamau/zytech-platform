# Quotations & Sales

> Phase 9 reference for the Zytech Contractors Platform.

## Purpose

The Quotation domain converts website visitors into qualified leads and supports the sales workflow from initial request through approval, delivery, and acceptance.

Public submissions create a `SalesLead`, `QuotationRequest`, and optional attachments. Staff build official `Quotation` records with sections, line items, approvals, and PDF document stubs.

## Domain

`App\Domains\Quotation` owns:

- Lead sources and sales leads
- Quotation requests (public intake)
- Official quotations (builder, pricing, approvals)
- Site visits
- Status history and document records

Business data is not cached — records stay fresh.

## Admin

Filament **Sales** group:

- Lead sources
- Quotation requests
- Quotations (sections, items, approve, send)
- Site visits

Permissions: `quotations.view` (staff) and `quotations.manage` (administrators).

## Public site

- `/quote` — Livewire `website.request-quotation-form`
- `/quote/success/{reference}` — confirmation page
- `/quote/track/{reference}` — Livewire `website.track-quotation`

The header **Request a Quote** CTA routes to `/quote`.

## Workflow

```text
Public form → Pending request → Review in Filament
    → Create quotation (Draft) → Approve → Send
    → PDF stub + email listeners (queued)
    → Accepted / Rejected
```

Reference numbers: `ZQR-*` for requests, `ZQ-*` for quotations.

## Seed

`QuotationSeeder` loads lead sources: Website, Phone, Referral, Walk-in.

The `quotations` feature flag is enabled via `ConfigurationSeeder`.

## Deferred

- Real PDF rendering (dompdf/snappy)
- Outbound mailable classes
- Client portal acceptance UI
- Project conversion action from accepted quotes
- Sales analytics dashboard widgets in Filament
- Digital signatures
- Tests (per project rules)
