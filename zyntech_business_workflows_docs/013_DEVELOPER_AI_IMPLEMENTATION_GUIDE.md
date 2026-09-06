# Developer & AI Agent Implementation Guide

## Purpose

This is the implementation contract for developers and AI coding agents.

## Architecture

Prefer a domain-oriented separation:

```text
Domain Rules
 -> Application Actions / Services
 -> Models & Persistence
 -> Policies / Authorization
 -> Events
 -> Notifications
 -> HTTP / Livewire / API
 -> UI
```

Business rules must not be scattered across controllers, Blade templates, or Livewire views.

## Core Models

Customer, Company, Contact, Product, ProductVariant, Cart, CartItem, RFQ, RFQItem, Quote, QuoteVersion, QuoteItem, PurchaseOrder, SalesOrder, SalesOrderItem, Invoice, InvoiceItem, Payment, Warehouse, Inventory, InventoryReservation, Fulfillment, Shipment, Delivery, Document, Comment, Notification, ActivityLog.

## Explicit Business Actions

Prefer explicit operations such as:

```text
SubmitRfq
CreateQuote
SendQuote
RequestQuoteRevision
AcceptQuote
RejectQuote
UploadPurchaseOrder
ValidatePurchaseOrder
CreateSalesOrder
ConfirmSalesOrder
IssueInvoice
RecordPayment
ReserveInventory
CreateFulfillment
ScheduleDelivery
AssignDriver
CompleteDelivery
```

## Transactions

Critical state-changing workflows involving multiple records should use database transactions.

Example:

```text
Accept Quote
 -> record acceptance
 -> create Sales Order
 -> create audit event
 -> trigger downstream process
```

Avoid half-completed transitions.

## Authorization

Always check actor and ownership.

Examples:

- Customer can accept only their own valid quote.
- Customer can request revision only when allowed.
- Staff can issue quotes according to permission.
- Inventory overrides require authorization.
- Payment recording requires appropriate permission.

## Idempotency

Design operations triggered by retries, webhooks, jobs, email links, or APIs so duplicates do not create duplicate Sales Orders, Payments, Deliveries, etc.

## Immutable History

Issued/accepted commercial documents must preserve historical meaning. Later product changes must not rewrite old commercial transactions.

## Events

Useful events include:

- RfqSubmitted
- QuoteSent
- QuoteRevisionRequested
- QuoteAccepted
- PurchaseOrderUploaded
- SalesOrderCreated
- SalesOrderConfirmed
- InvoiceIssued
- PaymentRecorded
- InventoryReserved
- FulfillmentCreated
- DeliveryScheduled
- DeliveryCompleted

Use events intentionally, not merely for abstraction.

## UI

Available actions must derive from business state and authorization.

Do not hard-code actions without validating the transition.

## AI Agent Rules

Before coding:

1. Identify the business object.
2. Identify current state.
3. Identify target state.
4. Identify actor and permissions.
5. Check related documents.
6. Find existing actions/services.
7. Reuse existing design-system components/recipes.
8. Add business-rule tests.
9. Add audit/notification behavior where needed.
10. Avoid duplicate concepts.

## Never

Do not create:

- One generic transaction table for RFQs/quotes/orders/invoices/deliveries.
- One giant service for every business workflow.
- Hidden state changes inside templates.
- Silent mutation of issued quotes/invoices.
- Duplicate customer/product concepts per workflow.

## Testing Priorities

Test:

- Valid/invalid transitions
- Authorization
- Quote version acceptance
- PO mismatch
- Sales Order creation
- Financial calculations
- Partial payments
- Inventory reservation
- Partial fulfillment
- Delivery completion
- Idempotency
- Audit creation
- Customer data isolation

## Definition of Done

A workflow feature is complete when:

- Domain rules are implemented
- Authorization exists
- State transitions are validated
- Database integrity is protected
- Critical tests exist
- Audit events exist where required
- Notifications exist where required
- Portal/UI reflects valid states
- API/mobile consumers can use the same business rules
- Documentation is updated
