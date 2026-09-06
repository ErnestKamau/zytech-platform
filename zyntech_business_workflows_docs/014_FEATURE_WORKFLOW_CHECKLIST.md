# Feature & Workflow Implementation Checklist

Use this before considering a commerce/business feature complete.

## 1. Business Understanding

- [ ] What problem is being solved?
- [ ] Which actor performs the action?
- [ ] Which business object owns it?
- [ ] What is the current state?
- [ ] What is the target state?
- [ ] Which documents are created/affected?

## 2. Domain

- [ ] Does an existing entity already represent the concept?
- [ ] Is a new entity genuinely required?
- [ ] Are business boundaries preserved?
- [ ] Are state transitions explicit?
- [ ] Is historical information protected?

## 3. Authorization

- [ ] Who can view?
- [ ] Who can create?
- [ ] Who can edit?
- [ ] Who can approve/accept?
- [ ] Who can cancel?
- [ ] Is tenant/customer isolation enforced?

## 4. Commercial Rules

- [ ] Pricing
- [ ] Tax
- [ ] Discounts
- [ ] Currency
- [ ] Payment terms
- [ ] Delivery charges
- [ ] Quote validity
- [ ] PO requirements
- [ ] Credit/prepayment/deposit rules

## 5. Inventory & Operations

- [ ] Does it affect stock?
- [ ] Does it create/release a reservation?
- [ ] Can it be partially fulfilled?
- [ ] Does it create a shipment?
- [ ] Does it affect delivery scheduling?

## 6. Documents

- [ ] Quote
- [ ] Purchase Order
- [ ] Sales Order
- [ ] Invoice
- [ ] Delivery documents
- [ ] Technical/product documents
- [ ] Proof of delivery

## 7. Notifications

- [ ] Customer notification
- [ ] Staff notification
- [ ] Portal notification
- [ ] Email deep link
- [ ] Expiry/reminder notification

## 8. Audit

- [ ] Creation logged
- [ ] State changes logged
- [ ] Acceptance/rejection logged
- [ ] Financial events logged
- [ ] Operational events logged

## 9. UI/UX

- [ ] State is obvious
- [ ] Next valid action is obvious
- [ ] Invalid actions are hidden/disabled appropriately
- [ ] Loading state exists
- [ ] Skeleton exists where appropriate
- [ ] Empty state exists
- [ ] Error state exists
- [ ] Success state exists
- [ ] Mobile layout works
- [ ] Accessibility requirements are met

## 10. Testing

- [ ] Happy path
- [ ] Invalid transition
- [ ] Unauthorized access
- [ ] Edge cases
- [ ] Partial operation
- [ ] Duplicate request
- [ ] Concurrent operation
- [ ] Failure/retry behavior
- [ ] Regression tests

## AI Pre-Code Contract

Before writing code, explain:

```text
Actor
Object
Current State
Action
Validation
New State
Side Effects
Audit
Notification
```

Only then implement.
