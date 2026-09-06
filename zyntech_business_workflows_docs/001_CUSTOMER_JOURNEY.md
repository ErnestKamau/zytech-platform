# Customer Journey

## Purpose

Define the customer's end-to-end experience.

## Direct Purchase

```text
Discover
 -> Product Details
 -> Add to Cart
 -> Cart Review
 -> Checkout
 -> Order Confirmation
 -> Payment / Payment Terms
 -> Fulfillment
 -> Delivery Tracking
 -> Completed
```

## RFQ Purchase

```text
Select Products / Describe Requirement
 -> Request RFQ
 -> RFQ Submitted
 -> Zyntech Review
 -> Quote Issued
 -> Customer Review
 -> Accept / Request Revision / Reject
 -> Optional PO
 -> Sales Order
 -> Invoice / Payment
 -> Fulfillment
 -> Delivery
```

## Portal as System of Record

The customer portal contains the authoritative workflow state. Email should notify the customer and link back to the relevant portal record.

Example:

```text
Quotation QT-2026-00124 is ready for your review.
[Review quotation]
```

## Portal Navigation

- Overview
- Orders
- RFQs
- Quotes
- Invoices
- Payments
- Deliveries
- Documents
- Projects
- Addresses
- Company Profile

## Customer Actions

Customers may, depending on state:

- Add products to cart
- Request an RFQ
- Respond to clarification
- Review quotes
- Download PDFs
- Accept quotes
- Request revisions
- Reject quotes
- Comment
- Upload POs
- View orders/invoices/deliveries
- Make payments
- Download documents

## UX Rule

Never expose an action that is invalid for the current state.

Examples:

- Rejected quote: no `Accept`
- Expired quote: no `Accept`
- Paid invoice: no `Pay`
- Completed delivery: no `Schedule Delivery`
