# Domain Model & State Machines

## Core Relationship

```text
Customer
 |
 +--> RFQ
 |     |
 |     +--> Quote
 |            |
 |            +--> Quote Version
 |
 +--> Sales Order <---- Accepted Quote
        |
        +--> Invoice
        |     |
        |     +--> Payment
        |
        +--> Fulfillment
              |
              +--> Shipment
              |
              +--> Delivery
```

## Product Side

```text
Category
 -> Product
    -> Product Variant
       -> Inventory
          -> Warehouse
```

## RFQ States

```text
Draft
Submitted
Under Review
Quoted
Cancelled
Closed
```

## Quote States

```text
Draft
Sent
Viewed
Revision Requested
Resent
Accepted
Rejected
Expired
Cancelled
```

## Sales Order States

```text
Draft
Confirmed
Processing
Partially Fulfilled
Fulfilled
Cancelled
```

## Invoice States

```text
Draft
Issued
Partially Paid
Paid
Overdue
Void
Cancelled
```

## Delivery States

```text
Pending
Scheduled
Assigned
Picked Up
In Transit
Delivered
Failed
Returned
```

## State Transition Contract

Every transition should define:

1. Current state
2. Allowed action
3. Actor/authorization
4. Validation
5. New state
6. Audit event
7. Notification where appropriate

## Status Separation

Do not use one overloaded status to represent commercial agreement, payment, inventory, fulfillment, and delivery.
