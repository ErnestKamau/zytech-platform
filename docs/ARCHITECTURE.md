# Zytech Contractors Platform

# 02_ARCHITECTURE.md

> Version: 1.0
> Last Updated: July 2026
> Related Documents:
>
> - 01_PROJECT.md
> - 03_DEVELOPMENT.md
> - 04_DATABASE.md

---

# Architecture Philosophy

The Zytech Contractors Platform is designed as a modular, scalable Laravel application that begins life as a premium marketing website and evolves into a complete construction management platform.

The architecture prioritizes:

- Scalability
- Maintainability
- Separation of Concerns
- Laravel Conventions
- Developer Experience
- Performance
- Testability

Every architectural decision should support future expansion without requiring major refactoring.

---

# High-Level Architecture

                     Internet
                          │
                          │
                Laravel Application
                          │
        ┌─────────────────┼──────────────────┐
        │                 │                  │
        ▼                 ▼                  ▼
 Marketing Website   Client Portal     Admin CMS
 (Livewire)          (Livewire)        (Filament)

                          │
                          ▼

                Application Services

                          │

      ┌──────────────┬───────────────┬──────────────┐

      ▼              ▼               ▼

 Domain Logic     Notifications     Media Library

                          │

                          ▼

                    Database Layer

                          │

                MySQL / PostgreSQL

---

# Domain Architecture

Instead of thinking in pages, the application is divided into business domains.

Each domain owns its own models, actions, policies, notifications and UI.

Core Domains

Home

Services

Projects

Media

Knowledge Centre

Testimonials

Clients

Quote Requests

Quotations

Documents

Notifications

Users

Settings

Future Domains

Inventory

CRM

Procurement

HR

Accounting

Equipment

Project Management

Reports

---

# Domain Boundaries

The platform consists of three major applications sharing one codebase.

## 1 Marketing Website

Purpose

Generate leads.

Responsibilities

- Showcase projects
- Explain services
- Publish articles
- Accept quote requests

---

## 2 Client Portal

Purpose

Improve customer communication.

Responsibilities

- View quotations
- Download documents
- Messaging
- Notifications
- Project progress

---

## 3 Internal Administration

Purpose

Manage business operations.

Responsibilities

- Projects
- Services
- Media
- Quotes
- Clients
- Blog
- Team
- Testimonials
- Users

Implemented entirely using Filament.

---

---

# Application Architecture

## Architecture Style

The Zytech Contractors Platform follows a hybrid architecture combining:

- Domain-Driven Design Lite (DDD Lite)
- Event-Driven Architecture (EDA)
- Modular Monolith
- Action-Based Application Services
- Laravel Conventions

The objective is to build software that can evolve into a complete Construction ERP without requiring a rewrite.

Although the application is deployed as a single Laravel application, it should behave internally as a collection of loosely coupled business modules.

---

# Core Architectural Principles

The architecture is guided by the following principles.

1. Business First

Business capabilities define the architecture.

Never organize the project around framework components.

Developers should think about:

- Projects
- Quotations
- Clients
- Company
- Services

—not—

- Controllers
- Models
- Requests

---

2. Modular Monolith

The platform is a Modular Monolith.

Every domain should be:

- Independent
- Self-contained
- Easily discoverable
- Testable
- Replaceable

Each module should own:

- Models
- Actions
- Services
- Policies
- Events
- Jobs
- Livewire Components
- Notifications
- Rules
- Tests

Modules communicate through events rather than direct dependencies whenever possible.

---

3. Event Driven

Business events are first-class citizens.

Every important business action should emit an event.

Example

```
Quote Accepted

↓

QuoteAccepted Event

↓

Listeners

↓

Queue Jobs

↓

Broadcast

↓

Notifications

↓

Cache Invalidation

↓

Activity Log
```

Events become the language through which domains communicate.

---

4. Laravel First

Laravel conventions remain the default.

Do not reinvent framework features.

Prefer:

Policies

Notifications

Queues

Broadcasting

Events

Eloquent

Validation

Route Model Binding

Resource Collections

before introducing custom abstractions.

---

5. Simplicity

Choose the simplest implementation that satisfies the business requirements.

Avoid unnecessary abstraction.

Avoid enterprise complexity.

Avoid premature optimization.

---

# Architectural Layers

The application is divided into four primary layers.

```text
app/

Core/

Domains/

Infrastructure/

Support/
```

Each layer has a clearly defined responsibility.

---

# Core Layer

The Core layer contains reusable application building blocks.

It should never contain business-specific logic.

Examples

```text
Core/

Actions/

Contracts/

Data/

Enums/

Exceptions/

Traits/

ValueObjects/

Concerns/

Interfaces/
```

The Core layer should remain stable.

---

# Domain Layer

The Domain layer contains all business capabilities.

Every feature belongs to exactly one domain.

Example

```text
Domains/

Company/

Project/

Quotation/

Client/

Knowledge/

Service/

Media/

Notification/

User/

Settings/
```

No domain should become dependent on another domain's internal implementation.

---

# Standard Domain Structure

Every domain follows the same structure.

```text
Project/

Actions/

Data/

Enums/

Events/

Exceptions/

Jobs/

Listeners/

Livewire/

Mail/

Models/

Notifications/

Policies/

Queries/

Rules/

Services/

Support/

Tests/

ValueObjects/
```

Consistency is more valuable than cleverness.

---

# Infrastructure Layer

Infrastructure contains technical implementations.

Examples

```text
Infrastructure/

Broadcasting/

Cache/

Filesystem/

Mail/

Queue/

Reverb/

Redis/

Search/

Storage/

ThirdParty/
```

Infrastructure can change without affecting business rules.

Changing Redis should not require changing Project logic.

Changing storage providers should not require changing Quotation logic.

---

# Support Layer

Support contains generic utilities.

Examples

```text
Support/

Builders/

Helpers/

Macros/

Providers/

Utilities/
```

Business logic should never live here.

---

# Module Independence

Every module should own its own logic.

The following rule applies throughout the project.

> A developer should be able to remove an entire domain with minimal impact on unrelated domains.

If removing one module breaks another, the architecture should be reconsidered.

---

# Domain Communication

Preferred communication.

```text
Livewire

↓

Action

↓

Event

↓

Listener

↓

Queue

↓

Broadcast

↓

Notification

↓

Cache

↓

Activity Log
```

Avoid:

Controller

↓

Service

↓

Service

↓

Service

↓

Service

↓

Model

Deep service chains indicate excessive coupling.

---

# Dependency Rules

Allowed

```
Domain

↓

Core
```

```
Domain

↓

Infrastructure (through interfaces where appropriate)
```

```
Domain

↓

Events
```

Not Allowed

```
Project Domain

↓

Quotation Domain Models
```

Instead

```
ProjectCreated

↓

Event

↓

Quotation Listener
```

---

# Business Logic

Business logic belongs in Actions and Services.

Avoid placing business rules inside:

- Controllers
- Livewire Components
- Filament Resources
- Blade Views
- Migrations

Models should remain thin.

Their responsibilities are:

- Relationships
- Casts
- Scopes
- Accessors
- Mutators

Heavy business logic belongs elsewhere.

---

# Event Catalogue

Every important business action should emit a domain event.

Examples

ProjectCreated

ProjectPublished

ProjectArchived

ProjectFeatured

QuoteRequested

QuoteReviewed

QuoteAccepted

QuoteRejected

QuoteConverted

ClientRegistered

MediaUploaded

MediaOptimized

KnowledgeArticlePublished

TeamMemberAdded

SettingsUpdated

These events become integration points for:

- Redis
- Laravel Reverb
- Queues
- Notifications
- Activity Logging
- Future APIs

---

# Package Philosophy

The framework should solve as much as possible.

When introducing packages:

1. Laravel first
2. First-party packages
3. Spatie packages
4. Mature community packages

Avoid obscure packages.

Avoid abandoned packages.

Avoid packages that duplicate Laravel functionality.

---

# Engineering Rule

Every class should answer three questions.

1.

Who owns me?

(Project Domain)

2.

Why do I exist?

(Publish a Project)

3.

Who depends on me?

(Event Listeners)

If these questions cannot be answered quickly, the design should be reconsidered.

---

# Guiding Principle

The platform should grow by adding modules—not by increasing complexity inside existing modules.

Architecture should make growth feel natural.

---
I would not put everything directly under app/Domains.

I recommend this instead:

app/

Core/
Domains/
Infrastructure/
Support/
Core/

Contains cross-cutting application concerns:

Core/

Actions/

Contracts/

Enums/

Exceptions/

Traits/

ValueObjects/

Helpers/
Domains/

Contains business logic only.

Domains/

Project/

Quotation/

Client/

Company/

Knowledge/

Media/

Service/

User/
Infrastructure/

Everything that interacts with external systems.

Infrastructure/

Cache/

Broadcasting/

Filesystem/

Mail/

Payments/

Search/

Reverb/

Redis/

Integrations/
Support/

General utilities.

Support/

Macros/

Providers/

Builders/

Utilities/
Why this is better

This separation keeps business logic distinct from technical implementation:

If you replace Redis, only Infrastructure/Redis changes.
If you replace Meilisearch, only Infrastructure/Search changes.
If you add AWS S3, it belongs in Infrastructure/Filesystem.
Your business domains remain unaffected.

This aligns closely with ideas from Hexagonal Architecture and Clean Architecture while still feeling like Laravel—not over-engineered, just well organized.
---



# Blade Components

resources/views/components/

layout/

navigation/

buttons/

cards/

forms/

sections/

icons/

gallery/

timeline/

maps/

Every reusable UI element becomes a Blade component.

---

# Filament Architecture

Filament is the operational backbone.

Resources

Dashboard

Projects

Services

Testimonials

Knowledge Centre

Clients

Quote Requests

Quotations

Documents

Media

Settings

Users

Widgets

Revenue

Projects

Quotes

Recent Activity

Lead Sources

Relation Managers

Project Images

Project Videos

Timeline

Documents

Testimonials

---

# Data Flow

Visitor

↓

Livewire Component

↓

Form Request

↓

Action

↓

Service

↓

Model

↓

Database

Never allow Livewire components to contain business logic.

---

# Actions

Actions encapsulate a single business operation.

Examples

CreateQuoteAction

ApproveQuotationAction

PublishProjectAction

UploadProjectMediaAction

GenerateProjectTimelineAction

Actions should remain small.

One action = one responsibility.

---

# Services

Services coordinate multiple Actions.

Example

QuotationService

may call

GenerateQuotePDFAction

SendQuotationEmailAction

NotifyClientAction

UpdateQuotationStatusAction

Services orchestrate.

Actions execute.

---

# DTO Strategy

Every complex request should become a DTO.

Examples

QuoteRequestData

ProjectData

ServiceData

ClientData

Benefits

Immutable

Typed

Reusable

Testable

---

# Value Objects

Avoid primitive obsession.

Examples

Money

PhoneNumber

Address

Coordinates

ProjectStatus

QuotationNumber

---

# Enums

Use PHP Enums.

Never store magic strings.

Example

ProjectStatus

Draft

Published

Archived

QuotationStatus

Pending

Review

Sent

Accepted

Rejected

Completed

---

# Policies

Every protected model must have a Policy.

Examples

ProjectPolicy

QuotePolicy

ClientPolicy

MediaPolicy

DocumentPolicy

Never authorize inside controllers.

---

# Event-Driven Design

Business events trigger side effects.

Example

QuoteRequested

↓

Generate Notification

↓

Send Email

↓

Create Activity Log

↓

Notify Admin

The originating Action should not know about downstream behavior.

---

# Notifications

Laravel Notifications should be used.

Channels

Database

Mail

Future

SMS

WhatsApp

Push Notifications

---

# Queues

The following operations must be queued.

Emails

PDF Generation

Image Optimization

Video Processing

SEO Sitemap Generation

Backups

Large Imports

---

# Media Architecture

Media is a first-class citizen.

Every project supports

Cover Image

Gallery Images

Videos

Drone Footage

Blueprint PDFs

Documents

Future

360 Tours

Each media type belongs to Media Library collections.

---

# Project Architecture

Project

↓

Gallery

↓

Timeline

↓

Services

↓

Media

↓

Testimonials

↓

Related Projects

Every project is effectively a miniature website.

---

# Quote Architecture

Visitor

↓

Quote Wizard

↓

Quote Request

↓

Admin Review

↓

Quotation

↓

PDF

↓

Client Portal

↓

Acceptance

↓

Future ERP

---

# Client Portal Architecture

Authentication

↓

Dashboard

↓

Projects

↓

Documents

↓

Messages

↓

Notifications

↓

Invoices

↓

Profile

---

# Settings Architecture

Avoid config files for editable content.

Use database-backed settings.

Company

Logo

Brand Colors

SEO

Contact Details

Social Media

Analytics

Email Settings

---

# Search Architecture

Future

Laravel Scout

↓

Meilisearch

Searchable

Projects

Articles

Services

FAQs

---

# SEO Architecture

Every page owns SEO metadata.

Title

Description

Canonical URL

Open Graph

Twitter Card

Schema.org

Images

No page should rely on defaults.

---

# Caching Strategy

Cache

Homepage

Featured Projects

Services

Settings

Navigation

Testimonials

Knowledge Articles

Use tagged cache where appropriate.

---

# Security Architecture

Validation

Authorization

Policies

Rate Limiting

CSRF

Sanitized Uploads

Signed URLs

Password Hashing

2FA (Future)

---

# Performance Strategy

Lazy Loading

Image Optimization

Responsive Images

Deferred Livewire Loading

Database Indexes

Queue Heavy Work

HTTP Caching

Minified Assets

---

# Testing Architecture

Tests/

Feature/

Unit/

Livewire/

Filament/

Actions/

Services/

Every Action should be testable independently.

---

# Logging

Activity Log

Authentication

Quote Requests

Status Changes

Media Uploads

Document Downloads

Errors

Use structured logging.

---

# Error Handling

Never expose exceptions.

Log everything.

Provide meaningful user feedback.

Gracefully recover where possible.

---

# Coding Boundaries

Business logic

→ Actions

Complex orchestration

→ Services

Presentation

→ Livewire

Reusable UI

→ Blade Components

Administration

→ Filament

Persistence

→ Models

Authorization

→ Policies

Validation

→ Form Requests

---

# Dependency Rules

Livewire Components

may depend on

Actions

Services

Models

Never

Other Livewire Components

Controllers

Business Logic

---

Actions

may depend on

Models

DTOs

Value Objects

Never

HTTP Requests

Views

Blade

---

Services

may depend on

Actions

Repositories (if introduced)

Notifications

Events

Jobs

---

Blade Components

may depend only on

View Data

Never

Database Queries

Business Logic

---

# Future ERP Readiness

Every module introduced today should eventually support:

Permissions

Activity Logs

Notifications

Attachments

Comments

API Access

Exports

Reports

This eliminates future rewrites.

---

# Naming Conventions

Models

Singular

Project

Service

Quotation

Actions

Verb + Noun + Action

CreateProjectAction

PublishProjectAction

GenerateQuotationPDFAction

Events

Past Tense

QuoteRequested

ProjectPublished

MediaUploaded

Jobs

Verb + Object + Job

OptimizeImagesJob

GeneratePDFJob

Mail

Descriptive

QuotationReadyMail

QuoteReceivedMail

---

# Architectural Principles

The project follows these principles.

1. Convention over Configuration

Prefer Laravel defaults.

2. Modular Design

Every feature is isolated.

3. Explicit over Implicit

Magic should be minimized.

4. Small Classes

One responsibility.

5. Composition over Inheritance

Prefer services and actions.

6. Testability

Everything should be independently testable.

7. Scalability

Every architectural decision should support future growth.

---

# Final Principle

The architecture should never optimize for today's requirements alone.

Every module should be designed with the assumption that the platform will eventually manage the entire lifecycle of a construction project—from first inquiry to quotation, approvals, execution, documentation, client communication, and long-term business operations.

The goal is not merely to build a website, but to establish a durable software foundation that can evolve alongside Zytech Contractors without requiring fundamental architectural changes.
