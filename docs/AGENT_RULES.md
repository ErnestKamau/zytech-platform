# Zytech Platform Engineering Constitution
## 99_AGENT_RULES.md

> Version 1.0

---

# Table of Contents

1. Purpose
2. Engineering Philosophy
3. Core Principles
4. Golden Rules
5. Architecture Rules
6. Domain Driven Design Rules
7. Laravel Rules
8. Livewire Rules
9. Blade Rules
10. Alpine.js Rules
11. Filament Rules
12. PostgreSQL Rules
13. Redis Rules
14. Queue Rules
15. Laravel Reverb Rules
16. Media Rules
17. API Rules
18. Security Rules
19. Performance Rules
20. UI/UX Rules
21. Accessibility Rules
22. Testing Rules
23. Git Workflow
24. Documentation Rules
25. AI Anti-Patterns
26. AI Completion Checklist
27. Definition of Done
28. Future Expansion Rules

---

# Purpose

This document defines the engineering standards for the Zytech Platform.

Every developer, AI coding assistant, and contributor must follow these rules.

The goals are:

- Maintain consistency
- Produce enterprise-quality software
- Minimize technical debt
- Ensure maintainability
- Maximize performance
- Prioritize security
- Encourage modularity
- Enable future scalability

If any generated code conflicts with this document, this document takes precedence.

---

# Engineering Philosophy

The platform exists to solve business problems, not to demonstrate technical cleverness.

Every engineering decision should optimize for:

- Simplicity
- Readability
- Maintainability
- Performance
- Security
- Scalability
- Testability
- Reusability

Always choose the solution that is easiest to understand six months later.

```

---

# Core Principles

```
Business First

↓

Architecture Before Features

↓

Security By Default

↓

Performance By Design

↓

Reuse Before Rewrite

↓

Events Over Tight Coupling

↓

Composition Over Inheritance

↓

Services Over Fat Controllers

↓

Queues Over Blocking Requests

↓

Observability Everywhere
```

---

# Golden Rules

## UUID First

Never expose numeric IDs.

Use UUID route model binding everywhere.

---

## Thin Controllers

Controllers orchestrate.

Nothing more.

---

## Thin Livewire Components

UI logic only.

Business logic belongs elsewhere.

---

## Thin Filament Resources

Resources define UI.

Never business rules.

---

## Services Own Business Logic

Complex business rules belong inside Services.

---

## Actions Perform One Task

Every Action performs one job.

No side effects.

---

## Events Broadcast Business Changes

Every significant business event dispatches a Laravel Event.

---

## Queue Heavy Operations

Never process expensive work during HTTP requests.

Use **Laravel Horizon** as the queue worker manager and operations dashboard.

---

## Observability

- Telescope → local debugging
- Pulse → application health (response times, slow queries, queues, cache, exceptions)
- Horizon → queue dashboards, failed jobs, retries, throughput, worker balancing

---

## Styling Split

- Public website & client portal → handcrafted CSS (Vite entrypoints under `resources/css/website` and `resources/css/portal`)
- Filament admin → Tailwind (Filament’s stack)

Never style the public site or portal with Tailwind utility classes.

---

## Cache Expensive Reads

Redis first.

Never cache transactional writes.

---

## Policies Everywhere

Every business resource must have a Policy.

Never authorize in controllers.

---

## Activity Logging

Every important business action must be auditable.

---

## Mobile First

Every UI must work on mobile.

---

## Accessibility Matters

Every page must meet WCAG AA.

---

## Documentation Is Mandatory

New features require documentation updates.

---

# Architecture Rules

(continued...)

# Architecture Rules

> Every feature developed for the Zytech Platform must conform to these architectural principles.

The architecture is intentionally modular, loosely coupled, event-driven, and domain-oriented.

Every implementation should strengthen—not weaken—the architecture.

---

# The Architectural Hierarchy

Business Requirement

↓

Domain

↓

Feature

↓

Models

↓

DTOs

↓

Repositories

↓

Services

↓

Actions

↓

Events

↓

Listeners

↓

Notifications

↓

Policies

↓

Livewire Components

↓

Blade Views

↓

Tests

↓

Documentation

No layer should be skipped without justification.

---

# Domain First Development

Every new feature belongs to a Domain.

Never build isolated features.

Examples

Company

Projects

Services

Clients

Quotations

Knowledge Centre

Media

Authentication

Configuration

Notifications

System

Every file should clearly belong to one domain.

Never create a "Miscellaneous" module.

---

# Folder Structure Rules

Every domain should follow a consistent internal structure under `app/Domains/{DomainName}/`.

Shared abstractions live in `app/Core/`.

Technical integrations live in `app/Infrastructure/`.

Generic utilities live in `app/Support/`.

Example domain layout:

```text
app/Domains/Project/
  Actions/
  Data/
  Enums/
  Events/
  Exceptions/
  Jobs/
  Listeners/
  Livewire/
  Models/
  Notifications/
  Policies/
  Queries/
  Rules/
  Services/
  Support/
  ValueObjects/
```

Never place unrelated classes together.

---

# Layer Responsibilities

Every layer has exactly one responsibility.

Models

↓

Represent data

Repositories

↓

Retrieve data

DTOs

↓

Transport structured data

Services

↓

Business rules

Actions

↓

Single operations

Events

↓

Announce business changes

Listeners

↓

React to events

Policies

↓

Authorization

Livewire

↓

Presentation

Blade

↓

Rendering

Resources

↓

Administration UI

Never mix responsibilities.

---

# Dependency Direction

Dependencies should always point downward.

Example

Livewire

↓

Service

↓

Repository

↓

Model

Never reverse dependencies.

Repositories should never know about Livewire.

Models should never know about Services.

Views should never know about Repositories.

---

# Feature Development Order

Always build features in this order.

Migration

↓

Model

↓

Policy

↓

DTO

↓

Repository

↓

Service

↓

Actions

↓

Events

↓

Listeners

↓

Notifications

↓

Livewire

↓

Filament Resource

↓

Tests

↓

Documentation

Skipping steps usually creates technical debt.

---

# Service Layer Rules

Business logic belongs only inside Services.

Services should:

Validate business rules

Coordinate repositories

Dispatch events

Start transactions

Trigger actions

Queue jobs

Return DTOs where appropriate

Services should never render UI.

---

# Repository Rules

Repositories own data retrieval.

Repositories should:

Build queries

Handle eager loading

Handle pagination

Handle searching

Handle filtering

Handle sorting

Never place business rules inside repositories.

---

# DTO Rules

Every complex operation should receive a Data Transfer Object.

DTOs should be:

Immutable

Strongly typed

Validated before use

Never pass large arrays between layers.

Never pass Request objects into Services.

---

# Action Rules

Actions perform one business operation.

Good examples

CreateProject

PublishProject

ApproveQuotation

GeneratePDF

SendQuotationEmail

ArchiveClient

Bad example

ProjectManager

Do not create "God" action classes.

Actions should be composable.

---

# Value Objects

Prefer Value Objects over primitive strings where appropriate.

Examples

Money

EmailAddress

PhoneNumber

Coordinates

Address

Measurement

ProjectStatus

QuotationStatus

ApprovalStatus

Future

GeoLocation

ColorPalette

WorkingHours

---

# Event-Driven Architecture

Every meaningful business action should dispatch an event.

Examples

ProjectCreated

ProjectUpdated

ProjectCompleted

QuotationRequested

QuotationAccepted

ClientRegistered

MediaUploaded

ArticlePublished

Events should describe something that already happened.

---

# Listener Rules

Listeners react to events.

Listeners may:

Send email

Generate PDFs

Create notifications

Clear Redis cache

Broadcast Reverb events

Queue AI processing

Update search indexes

Listeners should never modify unrelated business logic.

---

# Notification Rules

Notifications should be triggered by events.

Never send notifications directly from controllers.

Support:

Email

Database

Broadcast

Future

SMS

WhatsApp

Push Notifications

---

# Policy Rules

Every business resource must have a Policy.

Policies own authorization.

Never authorize inside:

Controllers

Services

Repositories

Blade

Livewire render methods

Policies should remain small and readable.

---

# Transactions

Wrap related business operations inside database transactions.

Example

Create Project

↓

Upload Media

↓

Assign Team

↓

Create Timeline

↓

Dispatch Event

↓

Commit

Rollback on failure.

---

# Exception Handling

Create domain-specific exceptions.

Example

ProjectAlreadyCompletedException

QuotationExpiredException

InvalidApprovalStateException

MediaConversionFailedException

Avoid generic exceptions when business meaning matters.

---

# Soft Deletes

Prefer Soft Deletes.

Deletion should be rare.

Use:

Archive

Deactivate

Suspend

Complete

before permanent deletion.

---

# UUID Rules

Every domain entity uses UUIDs.

UUIDs should be used for:

Routes

Relationships

Public APIs

Broadcast payloads

Search indexing

Never expose incremental IDs.

---

# Configuration Rules

Never hardcode:

URLs

Email addresses

Phone numbers

Company names

Currencies

Timezones

API keys

Store configuration inside the Configuration Domain.

---

# Cross-Domain Communication

Domains communicate through:

Events

Interfaces

DTOs

Never directly manipulate another domain's internals.

Example

Quotation Domain

↓

Dispatch Event

↓

Notification Domain

↓

Send Email

Instead of

Quotation Service

↓

Email Service

↓

SMTP

Loose coupling is preferred.

---

# AI Architecture Decision Tree

When implementing a feature, follow this decision process.

Need persistent data?

↓

Yes

↓

Create Migration

↓

Create Model

↓

Need business rules?

↓

Yes

↓

Create Service

↓

Need reusable operation?

↓

Yes

↓

Create Action

↓

Need side effects?

↓

Yes

↓

Dispatch Event

↓

Need email, notifications or indexing?

↓

Yes

↓

Create Listener

↓

Expensive operation?

↓

Yes

↓

Queue Job

↓

Need authorization?

↓

Yes

↓

Create Policy

↓

Need administration?

↓

Create Filament Resource

↓

Need customer interaction?

↓

Create Livewire Component

↓

Write Tests

↓

Update Documentation

Never bypass this workflow without a documented architectural reason.

---

# Architecture Quality Checklist

Before considering a feature complete, verify:

□ Domain identified

□ Migration created

□ UUIDs implemented

□ Model created

□ Policy implemented

□ DTOs used

□ Repository created

□ Service created

□ Actions extracted

□ Events dispatched

□ Listeners registered

□ Notifications implemented

□ Queued where appropriate

□ Horizon managing workers

□ Redis integrated

□ Reverb events broadcast

□ Pulse recording enabled (non-test)

□ Activity logged

□ Feature tested

□ Documentation updated

□ Architecture remains modular

□ Public/portal CSS follows handcrafted CSS standards (not Tailwind)