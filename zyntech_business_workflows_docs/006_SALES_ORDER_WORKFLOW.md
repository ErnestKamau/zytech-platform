# Sales Order Workflow

## Meaning

The Sales Order is Zyntech's confirmed transaction and operational commitment.

## Lifecycle

```text
Draft
 -> Confirmed
 -> Processing
 -> Partially Fulfilled
 -> Fulfilled
```

Alternative:

```text
Cancelled
```

## Creation Sources

- Direct checkout
- Accepted Quote

## Contents

Order number, customer/contact, source/reference, items, quantities, prices, discounts, taxes, delivery charges, total, currency, billing/delivery addresses, payment terms, fulfillment requirements, notes, documents.

## Traceability

The system should answer:

- Which RFQ produced this?
- Which quote version was accepted?
- Which PO authorized it?
- Which customer created it?
- Which invoice belongs to it?
- Which deliveries fulfill it?

## Downstream Operations

A confirmed Sales Order may trigger:

- Inventory reservation
- Fulfillment work
- Invoice generation
- Delivery scheduling
- Notifications

Commercial status and operational status should remain distinct.
