# Quote & Quote Versioning Workflow

## Meaning

A Quote is Zyntech's commercial offer. It is not yet a Sales Order.

## Lifecycle

```text
Draft
 -> Sent
 -> Viewed
 -> Revision Requested
 -> Resent
 -> Accepted
```

Terminal alternatives:

- Rejected
- Expired
- Cancelled

## Versioning

Never overwrite an issued quote.

```text
QT-00124 v1
 -> revision requested
QT-00124 v2
 -> revision requested
QT-00124 v3
 -> accepted
```

Only one version should become the accepted/effective version.

## Quote Contents

Customer, RFQ reference, quote number/version, validity, currency, items, quantities, prices, discounts, taxes, delivery costs, other charges, payment/delivery terms, notes, attachments, and terms/conditions.

## Customer Actions

Depending on state:

- View
- Download PDF
- Accept
- Request Revision
- Reject
- Comment
- Upload PO

## Acceptance Audit

Record accepted version, customer/contact, timestamp, acceptance method, and optional comment/PO reference.

## Critical Rule

Do not hard-code:

```text
Accepted Quote -> Invoice
```

The normal transition is:

```text
Accepted Quote
 -> Sales Order
 -> Commercial Rules
 -> Invoice / Payment
```
