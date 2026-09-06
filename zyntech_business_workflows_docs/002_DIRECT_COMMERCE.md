# Direct Commerce Workflow

## Flow

```text
Catalog
 -> Product
 -> Cart
 -> Checkout
 -> Sales Order
 -> Payment / Terms
 -> Fulfillment
 -> Delivery
 -> Completed
```

## Product Data

Products may contain:

- Name
- SKU
- Category
- Brand
- Description
- Unit of measure
- Price
- Tax configuration
- Stock availability
- Warehouse availability
- Weight/dimensions
- Images/videos
- Technical specifications
- Certifications
- Documents
- Related/compatible products

## Cart

Cart items retain product/variant, quantity, pricing snapshots, taxes, discounts, delivery information, and notes.

A Cart is not a Sales Order.

## Checkout

Collect/confirm customer, contact, billing/delivery address, items, pricing, taxes, delivery method, payment method/terms, and notes.

## Sales Order

Successful checkout creates a Sales Order. Historical commercial values must be preserved where required.

## Buy vs Request Quote

Products may support:

- `Add to Cart`
- `Request a Quote`

Some products can be direct-sale only, quote-only, or both.

## Inventory

Confirmed orders may create inventory reservations.

Conceptually:

```text
available = on_hand - reserved - unavailable
```

Do not treat physical stock as automatically available stock.
