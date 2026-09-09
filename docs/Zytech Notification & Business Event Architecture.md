# Zytech Notification & Business Event Architecture

## 1. Purpose

This document defines the notification, business-event, activity-history, and asynchronous delivery architecture for the Zytech application.

The system includes:

* Customer-facing ecommerce
* RFQ / quotation workflows
* Orders and checkout
* Customer accounts
* Employee/admin operations through Filament
* Employee follow-up and task tracking
* Historical activity/audit records
* Transactional email
* Future SMS and in-app notifications

The architecture MUST keep business logic independent from notification delivery providers.

Resend is an **email delivery provider**, not the application's notification/business-logic layer.

---

# 2. Core Architectural Principle

The application MUST follow this separation:

```text
BUSINESS ACTION
      │
      ▼
DOMAIN EVENT
      │
      ├──────────────► ACTIVITY / AUDIT HISTORY
      │
      └──────────────► NOTIFICATION POLICY
                              │
                     ┌────────┼────────┐
                     ▼        ▼        ▼
                   Email    In-App    SMS
                     │
                     ▼
                   Queue
                     │
                     ▼
                   Redis
                     │
                     ▼
              Laravel Worker
                     │
                     ▼
                  Resend
```

The application MUST NOT directly couple business operations to email delivery.

---

# 3. Three Distinct Concepts

## 3.1 Business Events

A business event represents a meaningful occurrence in the system.

Examples:

```text
UserRegistered
EmailVerified
PasswordResetRequested

RFQCreated
RFQAssigned
RFQQuotationPrepared
RFQQuotationSent
RFQApproved
RFQRejected
RFQExpired

OrderPlaced
OrderConfirmed
PaymentReceived
PaymentFailed
OrderProcessing
OrderShipped
OrderDelivered
OrderCancelled
RefundRequested
Refunded
```

Events represent facts:

> Something meaningful happened.

Events should NOT represent every database mutation.

Do NOT create events for insignificant changes such as:

```text
RFQ.updated_at changed
RFQ.notes changed
RFQ.title changed
```

unless those changes represent a meaningful business occurrence.

---

# 4. Activity / Audit History

Activity history answers:

> What happened, when did it happen, and who/what caused it?

Example:

```text
09 Sep 2026 09:21
RFQ created
Actor: System

09 Sep 2026 09:24
RFQ assigned to Mary
Actor: John

09 Sep 2026 11:42
Quotation prepared
Actor: Mary

09 Sep 2026 11:48
Quotation sent to customer
Actor: System

09 Sep 2026 14:12
Customer approved quotation
Actor: Customer
```

Activity history is particularly important for the Filament employee/admin application.

Employees must be able to understand the history of an RFQ/order/customer process.

Activity records MUST NOT depend on successful email delivery.

For example:

```text
RFQApproved
    │
    ├── Activity recorded
    │
    └── Email failed
```

The activity still exists because the RFQ was actually approved.

---

# 5. Notifications

Notifications answer:

> Who needs to know about this event?

One event can generate multiple notifications.

Example:

```text
RFQApproved
      │
      ├── Customer → Email
      │
      ├── Assigned Employee → In-App
      │
      └── Manager → Email + In-App
```

Therefore:

```text
Business Event != Notification
```

A business event happens once.

Multiple notifications can be generated from the same event.

---

# 6. Notification Channels

The notification system should support multiple delivery channels.

Initial channels:

```text
Email
In-App
```

Future channels:

```text
SMS
WhatsApp
Push Notifications
```

The system must be designed so adding another channel does not require modifying business logic.

Example:

```text
RFQApproved
      │
      └── Notification Policy
              │
              ├── Email
              ├── In-App
              └── SMS
```

---

# 7. Resend's Responsibility

Resend is responsible ONLY for email delivery.

Conceptually:

```text
Zytech Application
        │
        ▼
Notification System
        │
        ▼
Email Job
        │
        ▼
Resend API
        │
        ▼
Recipient Email Provider
```

Resend should NOT determine:

* when an RFQ is approved
* who should receive an RFQ notification
* whether an order is valid
* whether an employee should be assigned
* whether an activity should be recorded
* whether an RFQ should change status

Those are application responsibilities.

---

# 8. Business Actions / Services

Important business operations should be encapsulated in Actions/Services rather than placing business logic inside controllers or Filament components.

Recommended structure:

```text
app/
├── Actions/
│   ├── Orders/
│   │   ├── PlaceOrder.php
│   │   ├── ConfirmOrder.php
│   │   ├── CancelOrder.php
│   │   └── ShipOrder.php
│   │
│   └── RFQs/
│       ├── CreateRFQ.php
│       ├── AssignRFQ.php
│       ├── PrepareQuotation.php
│       ├── SendQuotation.php
│       ├── ApproveRFQ.php
│       └── RejectRFQ.php
```

A Filament action should call the appropriate business Action.

Example:

```php
ApproveRFQ::handle($rfq, $employee);
```

The Filament page should NOT contain the complete business workflow.

---

# 9. Example RFQ Approval Flow

When an employee approves an RFQ:

```text
Filament
   │
   ▼
ApproveRFQ Action
   │
   ├── Validate RFQ state
   ├── Validate permissions
   ├── Update RFQ
   ├── Create approval record
   ├── Record activity
   │
   └── Dispatch RFQApproved event
                │
                ▼
        Notification Policy
                │
        ┌───────┼────────┐
        ▼       ▼        ▼
     Customer Employee Manager
       Email    In-App    Email
        │
        ▼
       Queue
        │
        ▼
      Redis
        │
        ▼
      Worker
        │
        ▼
      Resend
```

---

# 10. Database Transaction Rule

Business state changes must be persisted before notification delivery is attempted.

Example:

```text
BEGIN TRANSACTION

    update RFQ status = approved

    create approval record

    create activity record

COMMIT
```

After the transaction succeeds:

```text
dispatch RFQApproved
```

Email failure MUST NOT roll back the business transaction.

For example:

```text
RFQ status:
APPROVED

Email:
FAILED
```

This is a valid system state.

The RFQ remains approved.

The failed notification can be retried independently.

---

# 11. Queue Architecture

Important external communications MUST be asynchronous.

Do NOT perform important email delivery synchronously inside the HTTP request.

Bad:

```php
Mail::to($customer)->send(...);
```

inside the main business operation.

Preferred:

```text
Customer Action
      │
      ▼
Laravel Request
      │
      ├── Save business state
      │
      └── Dispatch Job
                │
                ▼
              Redis
                │
                ▼
          Laravel Worker
                │
                ▼
             Resend
```

This ensures:

* customer requests remain fast
* temporary Resend failures do not break business transactions
* email jobs can be retried
* failed jobs can be investigated
* external services remain decoupled

---

# 12. Queue Retry Strategy

Notification jobs should support retries.

Example conceptual configuration:

```text
Attempt 1 → immediately
Attempt 2 → after 10 seconds
Attempt 3 → after 60 seconds
Attempt 4 → after 5 minutes
```

The exact retry configuration should be determined based on the application's operational requirements.

Permanent failures should be available through Laravel's failed-job mechanism for investigation.

---

# 13. Notification Records

The application should maintain its own notification records.

Do not rely exclusively on Resend's dashboard as the application's notification history.

A notification record should conceptually contain information such as:

```text
notification
----------------------------
id
type
recipient
channel
status
title
body/reference
queued_at
sent_at
delivered_at
failed_at
read_at
error
metadata
created_at
updated_at
```

The exact database schema must be designed before implementation.

Do not blindly copy the above fields without evaluating the application's requirements.

Notification records answer:

```text
Who was supposed to receive this?
Which channel was used?
What happened to the notification?
Was it sent?
Was it delivered?
Was it read?
Did it fail?
Why did it fail?
```

---

# 14. Activity vs Notification

These MUST remain separate.

Example:

```text
BUSINESS EVENT
RFQApproved
```

Activity:

```text
Customer approved RFQ #RFQ-000123
```

Notifications:

```text
Email → customer@example.com → delivered

In-App → employee #45 → read

Email → manager@example.com → failed
```

The activity describes the business event.

The notifications describe communication attempts.

---

# 15. Notification Policy

Not every business event should produce a notification.

Example:

```text
RFQViewed
```

May produce:

```text
Activity ✓
Notification ✗
```

While:

```text
RFQApproved
```

may produce:

```text
Activity ✓

Customer → Email ✓
Employee → In-App ✓
Manager → Email ✓
```

Therefore notification decisions should be controlled by notification policies/rules.

Conceptually:

```text
Event
  │
  ▼
Notification Policy
  │
  ├── Determine recipients
  ├── Determine channels
  ├── Determine template
  └── Create notification jobs
```

Do NOT scatter logic such as:

```php
if ($rfq->status === 'approved') {
    Mail::to(...)->send(...);
}
```

throughout controllers, models, Filament resources, or Livewire components.

---

# 16. Customer vs Employee Notifications

The system has different audiences.

## Customer

Examples:

```text
Your RFQ has been received.
Your quotation is ready.
Your quotation has been approved.
Your order has been confirmed.
Your payment was received.
Your order has shipped.
```

## Employee

Examples:

```text
New RFQ requires review.
RFQ assigned to you.
Customer approved quotation.
Payment received.
Order requires processing.
Order has been cancelled.
```

## Manager

Examples:

```text
High-value RFQ submitted.
RFQ approved.
Order cancelled.
Payment failed.
RFQ has been waiting too long.
```

The same event can therefore produce different messages for different recipients.

---

# 17. Notification Templates

Notification content should not be hardcoded throughout business logic.

For example:

```text
RFQApproved
    │
    ├── Customer Email Template
    │
    ├── Employee In-App Template
    │
    └── Manager Email Template
```

Templates should receive the relevant domain data.

Example:

```text
RFQ number
Customer name
Quotation amount
Approval date
Assigned employee
Relevant URLs
```

Do not duplicate business calculations inside email templates.

---

# 18. Idempotency

Notification jobs should be designed with duplicate execution in mind.

Queues can retry jobs.

Therefore the system must avoid unintentionally sending duplicate business notifications when possible.

For critical notifications, consider a unique business/event reference such as:

```text
event_id
notification_id
```

Example:

```text
RFQApproved
event_id = UUID
```

Notification records can reference that event.

This allows the system to determine whether a notification has already been created/sent.

---

# 19. UUIDs

The Zytech application uses UUIDs rather than exposing sequential numeric IDs for primary business entities.

Event and notification records should follow the project's UUID strategy.

Example:

```text
event.id
notification.id
activity.id
rfq.id
order.id
```

Use the project's established UUID implementation consistently.

Do not introduce a second ID strategy.

---

# 20. Recommended Application Structure

Initial structure:

```text
app/
├── Actions/
│   ├── Orders/
│   └── RFQs/
│
├── Events/
│   ├── Account/
│   ├── Orders/
│   └── RFQs/
│
├── Listeners/
│   ├── Activity/
│   ├── Notifications/
│   └── Orders/
│
├── Notifications/
│   ├── Account/
│   ├── Orders/
│   └── RFQs/
│
├── Jobs/
│   ├── Notifications/
│   └── Emails/
│
├── Services/
│   └── Notifications/
│
└── Models/
    ├── Activity.php
    └── Notification.php
```

The exact structure may be adjusted to match the existing Zytech codebase.

Do NOT restructure the entire application unnecessarily if an existing convention already works well.

---

# 21. Example Order Flow

```text
Customer places order
        │
        ▼
PlaceOrder Action
        │
        ├── Validate cart
        ├── Validate pricing
        ├── Create order
        ├── Create order items
        ├── Record activity
        │
        └── Dispatch OrderPlaced
                    │
                    ▼
             Notification Policy
                    │
          ┌─────────┴─────────┐
          ▼                   ▼
       Customer            Employee
          │                   │
        Email              In-App
          │
          ▼
        Queue
          │
          ▼
        Redis
          │
          ▼
       Worker
          │
          ▼
       Resend
```

---

# 22. Example Payment Flow

```text
Payment successful
        │
        ▼
Record payment
        │
        ├── Activity
        │
        └── PaymentReceived
                    │
             ┌──────┴──────┐
             ▼             ▼
          Customer       Finance
             │             │
           Email         In-App
```

Payment processing itself MUST NOT depend on email delivery.

---

# 23. Filament Integration

Filament is the employee/admin operational interface.

Filament should consume the business architecture rather than become the business architecture.

Example:

```text
Filament
   │
   ▼
Action
   │
   ▼
Domain/business operation
   │
   ├── Database state
   ├── Activity
   └── Event
```

Filament should provide interfaces for:

* viewing activities
* viewing notification history
* seeing failed notifications
* manually retrying appropriate notifications
* viewing RFQ history
* viewing order history
* assigning RFQs/orders
* following up with customers
* seeing employee actions
* tracking process status

---

# 24. Employee Auditability

The system must make it possible to determine:

```text
WHO
WHAT
WHEN
WHERE APPLICABLE
```

For example:

```text
Who:
Employee #123

What:
Assigned RFQ #456 to themselves

When:
2026-09-09 14:32:10

Source:
Filament

Result:
RFQ status changed to assigned
```

For customer actions:

```text
Actor:
Customer

Action:
Approved quotation

Entity:
RFQ #456

Timestamp:
2026-09-09 16:02:21
```

System-generated actions should identify the actor as the system where appropriate.

---

# 25. Email Provider Abstraction

Application business logic should not depend directly on Resend-specific implementation details.

Avoid:

```text
RFQApproved
    ↓
Resend API directly
```

Prefer:

```text
RFQApproved
    ↓
Notification
    ↓
Email Channel
    ↓
Email Delivery Service
    ↓
Resend
```

This makes it possible to replace Resend in the future without rewriting the RFQ/order business logic.

---

# 26. Current Temporary Email Configuration

Until Zytech has its production domain, a verified existing domain may be used temporarily.

Current temporary sender:

```text
eazybuy.org
```

Example:

```env
MAIL_MAILER=resend
MAIL_FROM_ADDRESS=auth@eazybuy.org
MAIL_FROM_NAME="Zytech"
RESEND_API_KEY=re_xxxxxxxxx
```

This is temporary infrastructure.

When the client purchases and verifies the official Zytech domain, change the sender to the new verified domain.

Example:

```env
MAIL_FROM_ADDRESS=auth@zytech.co.ke
MAIL_FROM_NAME="Zytech"
```

The application architecture must NOT be designed around `eazybuy.org`.

The temporary sender is configuration only.

---

# 27. Security Requirements

Never:

* hardcode the Resend API key
* commit `.env`
* expose API keys in frontend code
* expose API keys in Git
* send email directly from browser JavaScript
* allow users to choose arbitrary sender addresses
* trust notification recipient addresses without server-side validation

The Resend API key belongs on the server.

Example:

```env
RESEND_API_KEY=re_xxxxxxxxx
```

The frontend must never receive this value.

If an API key is exposed publicly, rotate it immediately.

---

# 28. Failure Handling

Notification failures must be observable.

Example:

```text
Email Job
   │
   ▼
Resend
   │
   X
 Failure
   │
   ▼
Retry
   │
   X
 Failure
   │
   ▼
Failed Job
```

The application should allow administrators to investigate:

```text
Notification
Recipient
Channel
Event
Failure reason
Attempts
Last attempted
```

Business state should remain intact.

---

# 29. Do Not Overuse Events

Events are useful, but creating an event for every method or model update creates unnecessary complexity.

Use events for meaningful business occurrences.

Good:

```text
OrderPlaced
PaymentReceived
RFQApproved
RFQRejected
OrderShipped
```

Potentially unnecessary:

```text
OrderNameUpdated
RFQUpdated
CustomerModelSaved
```

unless those actions have genuine business significance.

The goal is clarity, not maximum abstraction.

---

# 30. Do Not Overuse Livewire

Livewire/Filament should primarily handle UI interaction.

Do not place:

* notification orchestration
* email delivery
* complex business rules
* queue handling
* RFQ workflow logic

inside Livewire components.

Prefer:

```text
Livewire / Filament
        ↓
Action / Service
        ↓
Business Logic
        ↓
Event
        ↓
Listeners / Notifications / Jobs
```

---

# 31. Implementation Order

Implement this architecture incrementally.

## Phase 1 — Foundation

Create:

```text
Activity logging
Business Events
Actions/Services
Queue infrastructure
Redis
```

## Phase 2 — Email

Implement:

```text
Email Notifications
Email Jobs
Resend integration
Retry handling
Failure handling
```

## Phase 3 — Filament

Implement:

```text
Activity timeline
Notification history
Failed notification visibility
Relevant retry actions
```

## Phase 4 — In-App Notifications

Implement:

```text
Employee notifications
Unread counts
Notification center
Read/unread state
```

## Phase 5 — Additional Channels

Only when required:

```text
SMS
WhatsApp
Push
```

Do not build every channel before there is a business requirement.

---

# 32. Initial Event Catalog

The initial event catalog should include at least:

## Accounts

```text
UserRegistered
EmailVerificationRequested
EmailVerified
PasswordResetRequested
```

## RFQs

```text
RFQCreated
RFQAssigned
RFQQuotationPrepared
RFQQuotationSent
RFQApproved
RFQRejected
RFQExpired
```

## Orders

```text
OrderPlaced
OrderConfirmed
PaymentReceived
PaymentFailed
OrderProcessing
OrderShipped
OrderDelivered
OrderCancelled
RefundRequested
Refunded
```

This catalog should evolve with the actual business workflows.

Do not implement events merely because they appear in this document if the corresponding business workflow does not exist yet.

---

# 33. Architectural Rules for AI Agents

When implementing Zytech features, the AI agent MUST follow these rules:

1. Do not send important transactional email directly from controllers.

2. Do not place email delivery logic inside Filament resources or Livewire components.

3. Do not make business operations depend on successful email delivery.

4. Use business Actions/Services for important workflows.

5. Use meaningful domain events for important business occurrences.

6. Record important business activity independently of notifications.

7. Use queued jobs for external notification delivery.

8. Use Redis as the queue backend where configured.

9. Use Resend as the email delivery provider, not as the business notification system.

10. Keep customer notifications and employee notifications conceptually separate.

11. Make notification recipients and channels policy-driven rather than scattered conditionals.

12. Design notification delivery for retries and duplicate execution.

13. Maintain notification history in the Zytech database.

14. Never expose Resend credentials to the frontend.

15. Never hardcode API keys.

16. Use UUIDs according to the project's existing UUID strategy.

17. Do not introduce unnecessary abstractions or frameworks.

18. Do not overuse Livewire.

19. Preserve existing working architecture unless there is a clear reason to refactor.

20. Before implementing a new notification, determine:

    * What business event causes it?
    * Who receives it?
    * Which channel is required?
    * What activity should be recorded?
    * Should delivery be queued?
    * What happens if delivery fails?
    * Can the operation be safely retried?

---

# 34. ZYTECH BUSINESS EVENT RULE (Locked)

Every meaningful business transition MUST follow:

```text
Domain Service
    ↓
Database Transaction
    ├── Business State Mutation
    └── DomainActivity          ← same TX as the fact
    ↓
Successful Commit
    ↓
Domain Event                    ← afterCommit
    ↓
Explicit Listener
    ↓
CommunicationService
    ↓
notification_logs
    ↓
Queued Delivery
    ↓
Channel Provider (Resend / database / broadcast / future)
```

**Business fact + business activity = same database transaction.**

**Notification / delivery = after successful commit.**

Never make notification delivery a prerequisite for a successful business state transition.

If a domain service transaction rolls back, the corresponding DomainActivity MUST also disappear. Notifications must never cause that rollback.

## Locked decisions

| Topic | Decision |
|-------|----------|
| Activity system | `DomainActivity` / `ActivityLogger` → canonical business timeline |
| Spatie `activity_log` | Low-level auth/config audit only |
| `notification_logs` | Delivery history only; reuse, do not replace |
| `activity_feed` | Communication-related; not the business timeline |
| Orchestration | Existing domain Services (no parallel `app/Actions` tree) |
| Notification policy framework | Not yet — explicit listeners + `CommunicationService` |
| Event naming | Keep `Quotation*` in code; RFQ = product/UI language |
| Email path | `CommunicationService` → queue → Resend |
| Filament | Consumer/UI only — never owns business logic |
| First slice | Quotation |
| Second slice | Orders / ecommerce |

## Implementation order

1. Quotation vertical slice (reference pattern)
2. Order / ecommerce vertical slice
3. Filament timelines and employee follow-up UX
4. Preferences and additional channels

---

# 35. Golden Rule

The most important rule for the entire architecture is:

```text
BUSINESS LOGIC
      ≠
NOTIFICATION LOGIC
      ≠
EMAIL PROVIDER
```

The correct dependency direction is:

```text
Business Operation
       │
       ▼
     Event
       │
       ▼
Notification System
       │
       ▼
Delivery Channel
       │
       ▼
Provider
```

Therefore:

```text
RFQApproved
```

must mean:

> The RFQ was approved.

It must NOT mean:

> Send an email through Resend.

Resend is only one possible consequence of the business event.

---

# 36. Target Architecture

The target Zytech architecture is:

```text
                         ZYTECH
                            │
                ┌───────────┴───────────┐
                │                       │
           CUSTOMER APP            FILAMENT
                │                  EMPLOYEE APP
                │                       │
                └───────────┬───────────┘
                            │
                            ▼
                    ACTIONS / SERVICES
                            │
                            ▼
                     BUSINESS DOMAIN
                            │
                 ┌──────────┴──────────┐
                 │                     │
                 ▼                     ▼
             DATABASE              DOMAIN EVENTS
                 │                     │
                 │              ┌──────┼──────┐
                 │              ▼      ▼      ▼
                 │          Activity Notification
                 │                     │
                 │             ┌───────┼───────┐
                 │             ▼       ▼       ▼
                 │           Email   In-App    SMS
                 │             │
                 │           Queue
                 │             │
                 ▼             ▼
             PostgreSQL      Redis
                               │
                               ▼
                         Laravel Worker
                               │
                               ▼
                             Resend
                               │
                               ▼
                         Email Provider
```

This architecture should be treated as the foundation for Zytech's RFQ, ecommerce, order, customer-account, employee-operation, and notification workflows.
