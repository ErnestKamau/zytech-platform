# Purchase Order Workflow

## Purpose

Define how a customer's PO participates in the commercial workflow.

## PO Is Optional

Possible flows:

```text
Accepted Quote -> Sales Order
```

or:

```text
Accepted Quote
 -> Customer PO
 -> PO Validation
 -> Sales Order
```

## Structured PO Data

- PO number/date
- Customer/contact
- Quote reference
- Currency
- Total
- Delivery address
- Payment terms
- Customer references
- Notes
- Original PO document

## Validation

Where required, compare PO and accepted quote for:

- Customer
- Currency
- Items
- Quantities
- Prices
- Taxes
- Total
- Delivery requirements
- Payment terms

Differences must be visible and handled intentionally.

## Suggested States

- Not Required
- Awaiting PO
- Uploaded
- Under Review
- Accepted
- Rejected / Mismatch

## Rule

A PO is the customer's procurement document. It does not replace Zyntech's Sales Order.
