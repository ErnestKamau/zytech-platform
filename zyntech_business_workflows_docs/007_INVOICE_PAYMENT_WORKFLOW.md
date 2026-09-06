# Invoice & Payment Workflow

## Invoice Lifecycle

```text
Draft
 -> Issued
 -> Partially Paid
 -> Paid
```

Alternative states:

- Overdue
- Void
- Cancelled

## Payment Models

### Prepayment

```text
Sales Order -> Invoice -> Payment -> Fulfillment
```

### Credit

```text
Sales Order -> Fulfillment -> Invoice -> Payment
```

### Deposit

```text
Sales Order
 -> Deposit Invoice
 -> Payment
 -> Fulfillment
 -> Final Invoice
```

### Milestone / Project

```text
Sales Order
 -> Milestone
 -> Invoice
 -> Payment
 -> Next Milestone
```

## Critical Rule

Do not hard-code one universal commercial sequence. Payment terms/business rules determine the sequence.

## Invoice Data

Invoice number, customer, Sales Order, quote/PO references, items, quantities, prices, discounts, taxes, delivery charges, total, amount paid, amount due, currency, due date, payment terms.

## Payment Data

Invoice, customer, amount, currency, method, reference, date, status, notes.

Partial payments must be supported.

## Financial Integrity

Issued invoices must not be silently rewritten. Corrections should use the accounting mechanism adopted by the system.
