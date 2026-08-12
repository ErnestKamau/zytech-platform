# Clients (CRM Lite)

> Phase 10 reference for the Zytech Contractors Platform.

## Purpose

The Client domain is the single source of truth for customer information. Sales leads, quotation requests, and official quotations link to a `Client` record instead of duplicating contact data.

Public quote submissions automatically find or create a client profile and append a timeline event.

## Domain

`App\Domains\Client` owns:

- Client profiles (individual and company)
- Contacts, addresses, documents, notes
- Communication history and activity timeline
- Tags, groups, and project associations
- Notification preferences (for future portal use)
- Dashboard analytics snapshot (Redis cache)

## Admin

Filament **Clients** group:

- Clients (profile, contacts, addresses, notes)
- Documents
- Tags
- Groups

Permissions: `clients.view` (staff) and `clients.manage` (administrators).

## Integration

- `QuotationRequestService::submit()` calls `ClientService::findOrCreateFromLead()` and sets `client_id` on leads and requests.
- Timeline records `quotation-requested` events when a public form is submitted.
- Portal user linking is available via `AssignPortalAccess` (Phase 11 builds the public portal UI).

## Cache

`ClientAnalyticsService` caches dashboard counts under `clients.dashboard` (15 minutes). Listeners clear cache on client, document, and communication changes.

## Seed

`ClientSeeder` loads three sample clients with contacts, tags, groups, documents, and communications.

## Deferred

- Client portal UI (Phase 11)
- Merge clients action
- Real file uploads for documents (storage integration)
- Filament dashboard widgets
- Tests (per project rules)
