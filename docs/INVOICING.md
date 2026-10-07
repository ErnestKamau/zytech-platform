# Invoicing: Quotation → Proforma Invoice → Tax Invoice

Status: built (Oct 2026). Extends the Quotation pipeline documented in `docs/QUOTATIONS.md`.

## Pipeline

```
Quotation accepted (client, portal)
  → SalesOrder + draft Invoice created          (App\Domains\Commerce\Services\SalesOrderService)
  → Proforma Invoice auto-issued (ZPI-*)         (App\Domains\Commerce\Actions\GenerateProformaInvoiceFromQuotation)
       → PDF generated, emailed, WhatsApp'd (if configured)
  → Admin manually issues the final Tax Invoice  (InvoiceResource "issue" action)
       → PDF generated, emailed, WhatsApp'd (if configured)
```

The Proforma Invoice is evidence of what the client agreed to (used for the client's own PO/budget approval, bank deposit references, etc.) — it is **not** a VAT tax invoice. The final Invoice is the legally-issued tax document and is only created when an admin explicitly clicks "Issue" on `InvoiceResource`, never automatically.

## Documents

Both `ProformaInvoice` and `Invoice` PDFs are generated from shared Blade partials under `resources/views/components/documents/*-sheet.blade.php`, reused by:
- The actual PDF (via DomPDF, `resources/views/pdf/*.blade.php`)
- The admin Document Viewer (`App\Http\Controllers\Admin\DocumentViewerController`, full-width native HTML render, not an embedded PDF plugin)
- The portal document pages (quotation detail page; invoice/proforma stream-and-embed pages)

Kenyan tax-invoice fields (KRA PIN, VAT number) live on `Company` and `Client` (added Phase 1) and render on both Proforma and Tax Invoice PDFs.

## WhatsApp delivery

`App\Domains\Communication\Services\TwilioWhatsAppService` sends via Twilio's Content API (`sendTemplate`) — WhatsApp Business rules require a pre-approved template for any business-initiated message, so freeform text is never used here. Configure `TWILIO_SID`, `TWILIO_AUTH_TOKEN`, `TWILIO_WHATSAPP_FROM`, and the `TWILIO_TEMPLATE_*` env vars once templates are approved in the Twilio Console; until then, WhatsApp actions stay hidden/no-op and only Email fires.

PDFs are attached via a signed, time-limited (24h), unauthenticated route (`routes/web.php`, `documents.*`) so Twilio's media fetcher can retrieve them — not the normal session-authenticated admin/portal routes.

## Billing Calendar & audit log

- Admin: `/admin/billing-calendar` (Filament FullCalendar) — invoice due dates, proforma expiry, site visits.
- Portal: `/portal/billing-calendar` — the client's own due dates and expiries.
- Admin: `/admin/notification-logs` — read-only, filterable log of every notification attempt (channel, status, error), for audit/compliance.
