# Delivery & Logistics Workflow

## Lifecycle

```text
Pending
 -> Scheduled
 -> Assigned
 -> Picked Up
 -> In Transit
 -> Delivered
```

Alternative outcomes:

- Failed
- Returned

## Delivery Data

Delivery number, Sales Order, fulfillment/shipment reference, customer, delivery address, driver, vehicle, scheduled time, pickup time, delivery time, status, proof of delivery, notes.

## Driver Assignment

A delivery may be assigned to a driver, vehicle, and route. Driver/vehicle availability is an operational concern separate from the Sales Order.

## Tracking

When implemented, support:

- Driver location
- Route
- ETA
- Delivery events
- Geolocation timestamps

## Proof of Delivery

May capture recipient, timestamp, signature, photo, notes, and confirmation document.

## Failed Delivery

Record a reason and operational next step, such as:

- Customer unavailable
- Incorrect address
- Vehicle issue
- Damaged goods
- Customer refused delivery
