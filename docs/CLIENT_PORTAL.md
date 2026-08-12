# Client Portal

> Phase 11 reference for the Zytech Contractors Platform.

## Purpose

The Client Portal is a secure workspace where linked clients manage quotations, projects, documents, messages, meetings, and support from one place.

Access requires:

1. An authenticated user account
2. A `Client` profile with `user_id` set and `portal_access_granted_at` filled (`AssignPortalAccess`)
3. The `client_portal` feature flag enabled

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

Under `/portal` with `auth` + `EnsurePortalAccess`:

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

## Admin

Filament **Clients** group additions:

- Announcements
- Support tickets
- Meetings

Staff use existing `clients.view` / `clients.manage` permissions.

## Demo login

- Email: `client@zytech.local`
- Password: `password`
- Linked to sample client James Mwangi via `PortalSeeder`

## Seed

`PortalSeeder` grants portal access, publishes a welcome announcement, and seeds sample message, ticket, notification, and meeting data.

## Deferred

- Real file download streaming / signed URLs
- Livewire realtime (Reverb) message delivery
- MFA and trusted-device UX beyond existing account security pages
- Online quotation PDF rendering
- Tests (per project rules)
