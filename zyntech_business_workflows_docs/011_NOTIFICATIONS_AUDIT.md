# Notifications, Activity & Audit Trail

## Notification Principle

Email is a notification channel. The portal is the system of record.

## Notification Events

### RFQ

- Submitted
- Clarification requested
- Quote ready

### Quote

- Sent
- Viewed
- Revision requested
- Resent
- Accepted
- Rejected
- Expiring soon
- Expired

### Order

- Confirmed
- Processing
- Partially fulfilled
- Fulfilled

### Invoice

- Issued
- Payment received
- Partial payment
- Due soon
- Overdue

### Delivery

- Scheduled
- Driver assigned
- In transit
- Completed
- Failed

## Audit Events

Record meaningful events such as:

```text
Quote created
Quote sent
Quote viewed
Quote downloaded
Revision requested
Quote revised
Quote accepted
PO uploaded
PO validated
Sales Order created
Invoice issued
Payment recorded
Delivery assigned
Delivery completed
```

## Audit Data

Where appropriate:

- Actor
- Actor type
- Action
- Entity
- Entity ID
- Timestamp
- Previous state
- New state
- Metadata
- Security-related request information where justified

## Rule

Application logs are not a replacement for a business audit trail.
