# Inventory & Fulfillment Workflow

## Inventory Concepts

Distinguish:

- On-hand
- Reserved
- Available
- Damaged/unavailable
- In transit
- Warehouse-specific stock

## Reservation

Example:

```text
On Hand: 100
Reserved: 30
Available: 70
```

Reservations should be released when appropriate.

## Warehouse Operations

Support, where required:

- Multiple warehouses
- Warehouse stock
- Transfers
- Adjustments
- Replenishment
- Picking
- Packing

## Fulfillment Flow

```text
Sales Order
 -> Inventory Check
 -> Reservation
 -> Picking
 -> Packing
 -> Shipment / Delivery
```

## Partial Fulfillment

Orders may have:

- In-stock items
- Backordered items
- Separate deliveries

Do not force every Sales Order into one delivery.

## Important Rule

Commercial, payment, fulfillment, and delivery statuses are separate dimensions.

Example:

```text
Sales Order: Confirmed
Payment: Partially Paid
Fulfillment: Partially Fulfilled
Delivery: In Transit
```
