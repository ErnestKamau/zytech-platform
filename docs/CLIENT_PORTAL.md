# Client Portal

> Phase 11 reference for the Zytech Contractors Platform.

## Purpose

The Client Portal is a secure workspace where linked clients manage quotations, projects, documents, messages, meetings, and support from one place.

Access requires:

1. An authenticated user account
2. A verified account (`verified` middleware) — first visit to `/email/verify` lets the user choose **Email** or **SMS** OTP
3. A `Client` profile with `user_id` set and `portal_access_granted_at` filled (`AssignPortalAccess`)
4. The `client_portal` feature flag enabled

Optional: Email/SMS OTP 2FA when enabled under `/account/security` (user chooses channel at login).

## Domain

`App\Domains\Portal` owns:

- Dashboard snapshot (Redis cached per client)
- Messaging (conversations + messages)
- Support tickets + replies
- Meeting slots and requests
- Portal notifications
- Document download logging
- Favorites
- Public announcements

## Routes

Under `/portal` with `auth` + `verified` + `EnsurePortalAccess`:

| Path | Page |
|------|------|
| `/portal` | Dashboard |
| `/portal/quotations` | Quotations (accept / reject when Sent) |
| `/portal/projects` | Linked projects |
| `/portal/documents` | Client-visible documents |
| `/portal/messages` | Messaging centre |
| `/portal/meetings` | Meeting requests |
| `/portal/support` | Support tickets |
| `/portal/notifications` | Notification centre |
| `/portal/timeline` | Client CRM timeline |

Account pages remain at `/account/*` (profile, security, sessions, settings).

## UI shell

Handcrafted CSS in `resources/css/portal/app.css` (shared `--zy-*` tokens from the website).

**Shell:** Glass icon rail (desktop **icons-only by default**, expand for labels) + **full-height hamburger** toggle strip + frosted content stage + topbar + mobile labeled drawer. Collapse preference stored in `localStorage` (`zy-portal-nav-collapsed`).

**Page recipes**

| Page | Pattern |
|------|---------|
| Dashboard | Sage→olive glass hero, tinted metric tiles, asymmetric list + activity split |
| Projects | Featured cards + progress-segment grid; search / filter / Excel export |
| Documents | Upload zone + icon tiles; Lottie empty; search / filter / Excel; real download stream |
| Quotations | Status-pill rows; View / Download / Print PDF (DomPDF); search / filter / Excel |
| Messages / Support | Master–detail split with elevated selection |
| Notifications | Toolbar + unread filters + Excel export; Lottie empty |
| Account | Narrow glass form cards with icon page headers |

Shared: `<x-portal.list-toolbar>`, `<x-ui.empty-state>` / `<x-ui.skeleton-grid>` on list loading. Icons are Heroicons via `x-portal.icon`. Glass is limited to shell, hero, and panels.

## Files & PDF

- Portal document upload → `storage/app/client-documents/{client_id}` with `visibility=client`
- Download: `GET /portal/documents/{document}/download`
- Quotation PDF: DomPDF via `QuotationPDFService`; stream `…/quotations/{id}/pdf`, download `…/pdf/download`
- Filament Clients → Documents: FileUpload + CSV ImportAction (`ClientDocumentImporter`)

## Admin

Filament **Clients** group additions:

- Announcements
- Support tickets
- Meetings
- Documents (upload + CSV import)

Staff use existing `clients.view` / `clients.manage` permissions.

## Demo login

- Email: `client@zytech.local`
- Password: `password`
- Linked to sample client James Mwangi via `PortalSeeder`

## Seed

`PortalSeeder` grants portal access, publishes a welcome announcement, and seeds sample message, ticket, notification, and meeting data.

## Deferred

- Livewire realtime (Reverb) message delivery beyond toast host
- Trusted-device “remember this browser” bypass
- Tests (per project rules)
