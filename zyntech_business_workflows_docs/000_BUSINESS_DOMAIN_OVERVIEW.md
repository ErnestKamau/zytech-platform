# Zyntech Business Domain & Commerce Overview

## Purpose

This is the high-level business/domain reference for Zyntech's construction-products commerce platform.

Zyntech is a **hybrid B2B commerce, procurement, fulfillment, and project-commerce platform**. It must support ordinary e-commerce and negotiated/project-based transactions.

## Core Journeys

### Direct Commerce

```text
Browse Products
 -> Product
 -> Cart
 -> Checkout
 -> Sales Order
 -> Payment / Payment Terms
 -> Fulfillment
 -> Delivery
 -> Completed
```

### Negotiated / Project Commerce

```text
Request RFQ
 -> RFQ
 -> Zyntech Review
 -> Quote
 -> Customer Review
 -> Accept / Request Revision / Reject
 -> Optional Purchase Order
 -> Sales Order
 -> Invoice / Payment
 -> Fulfillment
 -> Delivery
 -> Completed
```

## Domain Boundaries

- **RFQ** — customer asks Zyntech to price something.
- **Quote** — Zyntech makes a commercial offer.
- **Purchase Order** — customer's procurement authorization/document.
- **Sales Order** — Zyntech's confirmed commitment to fulfill.
- **Invoice** — request for payment.
- **Payment** — money received/recorded against an invoice.
- **Fulfillment** — preparation/allocation of goods or services.
- **Delivery** — movement and handover of goods.

Do not collapse these into one generic transaction.

## Core Entities

Customer, Company, Contact, Address, Product, Product Variant, Category, Brand, Cart, Cart Item, RFQ, RFQ Item, Quote, Quote Version, Quote Item, Purchase Order, Sales Order, Sales Order Item, Invoice, Invoice Item, Payment, Warehouse, Inventory, Inventory Reservation, Shipment, Delivery, Document, Comment, Notification, Activity/Audit Log.

## Non-Negotiable Principles

1. Preserve business-document boundaries.
2. Never silently change an issued commercial document.
3. Use versioning for quotes.
4. Customer Portal is the system of record for customer workflow.
5. Email is primarily notification/deep-link infrastructure.
6. Important commercial actions must be auditable.
7. Inventory must account for reservations.
8. Accepted quotes normally create Sales Orders before invoicing.
9. Support prepayment, credit, deposits, and milestone/project billing.
10. Direct commerce and RFQ commerce share domain data but retain distinct workflows.

## Implementation Rule

Before adding a feature, determine which business object owns the behavior. Do not attach unrelated behavior to a generic `Order` model for convenience.
