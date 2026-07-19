# Zytech Contractors Platform

> **Document:** Development Guide
>
> **Version:** 1.0.0
>
> **Laravel:** 13.x
>
> **PHP:** 8.4+
>
> **Last Updated:** July 2026

---

# Table of Contents

- Purpose
- Development Philosophy
- Core Principles
- Project Goals
- Technology Stack
- Development Environment
- Package Standards
- Coding Standards
- Architecture Standards
- Laravel Standards
- Livewire Standards
- Filament Standards
- Blade Standards
- CSS Standards (Public Website & Portal)
- Filament Tailwind Standards (Admin Only)
- Horizon Standards
- Pulse Standards
- Redis Standards
- PostgreSQL Standards
- Security Standards
- Performance Standards
- Testing Standards
- Git Workflow
- CI/CD
- Deployment
- Documentation Standards
- AI Development Guidelines
- Developer Checklist

---

# Purpose

This document defines the official development standards for the Zytech Contractors Platform.

It exists to ensure every contributor—whether a human developer or an AI coding agent—produces software that is:

- Maintainable
- Secure
- Performant
- Consistent
- Extensible
- Well documented
- Testable

Every implementation within this project should conform to the standards outlined in this document.

This document is considered the source of truth for development practices.

---

# Project Philosophy

The platform is not being built as a traditional company website.

Instead, it is being developed as a long-term software platform whose initial public interface is a premium marketing website.

Future phases will expand into:

- Client Portal
- Quotation Management
- Project Collaboration
- Construction ERP
- Mobile Applications
- Reporting & Analytics

Because of this vision, every implementation must be designed with future extensibility in mind.

---

# Engineering Principles

The following principles guide all development decisions.

## Simplicity

Choose the simplest solution that satisfies the requirements.

Avoid unnecessary abstraction.

Avoid clever code.

Readable code is better than impressive code.

---

## Laravel First

Always prefer Laravel's built-in features before introducing external solutions.

Examples:

- Policies before custom authorization
- Notifications before custom messaging
- Events before manual callbacks
- Queues before synchronous processing
- Collections before manual loops

Follow the framework.

Do not fight it.

---

## Convention Over Configuration

Prefer Laravel conventions whenever possible.

Developers should immediately recognize project structure without needing additional explanation.

---

## Composition Over Inheritance

Favor small reusable services and actions instead of large inheritance trees.

---

## Small Focused Classes

Every class should have one responsibility.

Examples:

Good

CreateQuotationAction

PublishProjectAction

GenerateProjectTimelineAction

Bad

ProjectManagerService

UtilityService

HelpersService

---

## Explicit Code

Code should communicate intent.

Avoid hidden behaviour.

Avoid magic.

Prefer explicit dependencies.

---

## Long-Term Maintainability

Never optimize for today's requirements alone.

Every implementation should support future expansion.

---

# Development Goals

Every feature should improve one or more of the following:

- User Experience
- Performance
- Security
- Maintainability
- Scalability
- Accessibility
- SEO
- Developer Experience

---

# Quality Standards

Every completed feature should meet the following criteria.

| Requirement | Status |
|-------------|---------|
| Mobile Responsive | Required |
| Accessible | Required |
| SEO Optimized | Required |
| Tested | Required |
| Documented | Required |
| Cached where appropriate | Required |
| Authorized | Required |
| Validated | Required |

---

# Clean Code Principles

The project follows:

- SOLID
- DRY
- KISS
- YAGNI
- PSR-12

In addition:

- Prefer immutable objects.
- Avoid duplication.
- Use expressive names.
- Remove dead code.
- Never leave TODOs in production code.

---

# Software Design Principles

Every feature should be:

Independent

Reusable

Replaceable

Discoverable

Documented

Testable

---

# Development Workflow

Every new feature should follow this sequence.

```text
Requirement

↓

Planning

↓

Architecture

↓

Database

↓

Backend

↓

Frontend

↓

Testing

↓

Documentation

↓

Review

↓

Merge
```

Skipping steps is discouraged.

---

# Definition of Done

A feature is considered complete only when:

- Requirements are satisfied.
- Code follows project standards.
- Tests pass.
- Authorization is implemented.
- Validation exists.
- Documentation is updated.
- Performance has been considered.
- Security has been reviewed.
- Responsive design has been verified.
- Accessibility has been checked.

---

# Architectural Mindset

Think in domains.

Not pages.

Bad

Home Page

Projects Page

Contact Page

Good

Project Domain

Quotation Domain

Client Domain

Knowledge Domain

Service Domain

Media Domain

---

# Preferred Development Order

Features should generally be developed in this order:

1. Database
2. Models
3. Enums
4. DTOs
5. Actions
6. Services
7. Policies
8. Livewire Components
9. Blade Components
10. Tests
11. Documentation

---

# Golden Rule

Every line of code should answer one question:

> Will this still be a good solution when the platform grows from a marketing website into a complete construction management system?

If the answer is "no", reconsider the implementation.

# Technology Stack

The Zytech Contractors Platform is built using a modern Laravel ecosystem that prioritizes performance, maintainability, scalability, developer experience, and real-time communication.

The selected technologies are actively maintained, align with Laravel's first-party ecosystem, and support the long-term vision of evolving from a corporate website into a complete Construction ERP platform.

---

# Stack Overview

| Layer | Technology | Purpose |
|---------|------------|---------|
| Backend | Laravel 13.x | Application Framework |
| Language | PHP 8.4+ | Backend Language |
| Frontend | Livewire 4 | Reactive UI |
| JavaScript | Alpine.js | Lightweight Interactivity |
| Public / Portal Styling | Handcrafted CSS | Website & client portal design system |
| Admin Styling | Filament Tailwind | Admin panel UI only |
| Build Tool | Vite | Asset Bundling |
| Admin Panel | Filament 5 | Administration & CMS |
| Database | PostgreSQL 16+ | Primary Database |
| Cache | Redis | Application Cache |
| Queue | Redis + Laravel Horizon | Queue Backend & Worker Dashboard |
| Sessions | Redis | Session Storage |
| Broadcasting | Laravel Reverb | WebSockets & Real-time Events |
| Monitoring | Telescope (local) + Pulse | Debugging & application health |
| WebSockets | Laravel Reverb | Live Updates |
| Search | Laravel Scout + Meilisearch *(Future)* | Full-text Search |
| Storage | Local / S3 Compatible | Media Storage |
| Web Server | Apache 2 | Production Web Server |
| Testing | Pest | Testing Framework |
| Static Analysis | Larastan + PHPStan | Static Code Analysis |
| Formatting | Laravel Pint | Code Formatting |

---

# Why This Stack?

Every technology is intentionally selected to maximize developer productivity, application performance, and future scalability.

The stack follows Laravel's first-party ecosystem wherever possible to minimize maintenance overhead and ensure long-term compatibility.

---

# Laravel

Laravel is the foundation of the platform.

It provides:

- Elegant architecture
- Rich ecosystem
- First-party packages
- Event-driven design
- Queue system
- Notifications
- Broadcasting
- Scheduling
- API support
- Excellent testing capabilities

Laravel conventions should always take precedence over custom implementations.

---

# PHP 8.4

The application targets PHP 8.4 or newer.

Modern language features should be embraced throughout the project.

Preferred features include:

- Enums
- Readonly Properties
- Constructor Property Promotion
- Match Expressions
- Typed Properties
- Named Arguments
- First-class Callables
- Modern Exception Handling

Legacy PHP patterns should be avoided.

---

# Livewire 4

Livewire is the primary frontend framework.

Use Livewire whenever server-driven rendering provides the best balance of developer productivity, performance, and maintainability.

Ideal use cases include:

- Forms
- Multi-step Wizards
- Search
- Filters
- Pagination
- Client Dashboard
- Quote Tracking
- Notifications
- Dynamic Galleries
- Dashboards
- Real-time Components

Static content should remain Blade-rendered.

---

# Alpine.js

Alpine.js complements Livewire by providing lightweight client-side interactions.

Use Alpine for:

- Dropdowns
- Accordions
- Modals
- Mobile Navigation
- Tabs
- Theme Switching
- Before / After Sliders
- Image Galleries
- UI State

Business logic must never be implemented in Alpine.

---

# Styling Strategy

The platform uses **two styling pipelines** on purpose.

## Public Website & Client Portal — Handcrafted CSS

The marketing website and client portal use organized, handcrafted CSS compiled by Vite.

Reasons:

- Full control over brand expression and motion
- Smaller, intentional CSS surface for public pages
- Avoids utility-class sprawl on marketing/content pages
- Clear separation from Filament’s admin UI

CSS lives under:

```text
resources/css/website/
resources/css/portal/
```

Recommended structure:

```text
website/
├── app.css
├── base/          # tokens, reset, typography
├── layout/        # header, footer, grids
├── components/    # buttons, forms, cards
├── pages/         # homepage, projects, services
└── utilities/

portal/
├── app.css
└── ...            # portal-specific modules (import shared tokens)
```

Rules:

- Prefer CSS custom properties (design tokens) for colors, spacing, and type
- Prefer semantic class names over utility soup
- Scope page styles; avoid global leakage
- Support `prefers-reduced-motion`
- Critical CSS / above-the-fold performance for the homepage

Do **not** style the public website or portal with Tailwind utility classes.

---

# Filament Admin — Tailwind CSS

Filament continues to use Tailwind CSS for the administration panel.

- Admin UI customization may use Filament’s theme / Tailwind tooling
- Tailwind in `package.json` exists for Filament theme work only
- Never import Filament Tailwind into public website or portal entrypoints

---

# Filament

Filament powers all internal administration.

Responsibilities include:

- Dashboard
- Projects
- Services
- Clients
- Quote Requests
- Quotations
- Knowledge Centre
- Testimonials
- Media Library
- Documents
- Team Members
- User Management
- Application Settings

Filament should be considered the default solution for all administration functionality.

---

# PostgreSQL

PostgreSQL is the official relational database.

Reasons include:

- Excellent relational integrity
- JSONB support
- Advanced indexing
- Full-text search
- GIS capabilities
- High performance
- Mature ecosystem

Development Environment

- PostgreSQL 16+

Production

- PostgreSQL 16+

Future versions may upgrade independently without requiring application changes.

---

# Redis

Redis is a mandatory infrastructure component.

Redis is not an optimization added later—it is part of the platform architecture.

Redis is responsible for:

- Application Cache
- Queue Backend
- Session Storage
- Rate Limiting
- Atomic Locks
- Cache Tags
- Temporary Data
- Broadcast Scaling
- Future Presence Channels

Redis is never the source of truth.

Persistent data always belongs in PostgreSQL.

---

# Laravel Reverb

Real-time communication is built using Laravel Reverb.

The platform adopts an event-driven architecture from the beginning.

Primary use cases include:

- Live Quote Status Updates
- Client Notifications
- Admin Notifications
- Dashboard Widgets
- Project Progress Updates
- Activity Feeds
- Team Collaboration
- Live Statistics
- Real-time Filament Widgets
- Future Chat System

Broadcasting should always occur through Events.

Components should react to events rather than polling wherever practical.

---

# Broadcasting Strategy

Broadcasting should be event-driven.

Example

QuoteCreated

↓

QuoteSubmitted Event

↓

Broadcast

↓

Admin Dashboard Updates

↓

Notification Sent

↓

Activity Logged

Never broadcast directly from controllers or Livewire components.

---

# Queue Strategy

The application follows a Queue-First architecture.

Expensive tasks should never execute during an HTTP request.

Always queue:

- Emails
- Notifications
- Image Optimization
- Video Processing
- PDF Generation
- Thumbnail Creation
- Sitemap Generation
- Backups
- Report Exports
- Search Index Updates
- Broadcast Events when appropriate

Redis is the primary queue backend.

**Laravel Horizon** is the official queue dashboard and worker manager.

Horizon provides:

- Queue dashboards
- Failed jobs inspection
- Retries
- Throughput monitoring
- Worker balancing
- Job metrics over time

Local development:

```bash
php artisan horizon
```

Or via `composer dev` (Horizon replaces `queue:listen`).

Production:

- Supervisor runs `php artisan horizon`
- Deployments should call `php artisan horizon:terminate`

Never process expensive work synchronously when a queue is available.

---

# Monitoring Strategy

## Telescope (Local / Debugging)

Laravel Telescope is for **local development debugging**.

It inspects:

- Requests
- Queries
- Jobs
- Mail
- Exceptions
- Cache / Redis interactions

Telescope should remain disabled (or tightly gated) in production.

## Pulse (Application Health)

Laravel Pulse complements Telescope by focusing on **ongoing application health**.

It tracks:

- Response times
- Slow database queries
- Queue performance
- Cache usage
- Exceptions
- Overall application metrics

Pulse is valuable in staging and production for operational awareness.

Dashboards:

| Tool | Path | Role |
|------|------|------|
| Horizon | `/horizon` | Queues & workers |
| Pulse | `/pulse` | Application health |
| Telescope | `/telescope` | Local debugging |
| Filament | `/admin` | Business administration |

Access is gated (`viewHorizon`, `viewPulse`, `viewTelescope`).

---

# Development Environment

Minimum Requirements

| Requirement | Version |
|------------|----------|
| PHP | 8.4+ |
| Composer | Latest Stable |
| Node.js | Latest LTS |
| npm | Latest |
| PostgreSQL | 16+ |
| Redis | Latest Stable |
| Git | Latest |
| Laravel | 13.x |

---

# Required Local Services

Every developer should have the following services available during development:

- PostgreSQL
- Redis
- Mailpit
- Vite Development Server
- Laravel Horizon
- Laravel Reverb
- Scheduler
- Laravel Pulse (recording enabled)

Optional

- Meilisearch
- Telescope (local debugging)

---

# Production Infrastructure

The production environment is standardized as follows.

| Component | Technology |
|------------|------------|
| Operating System | Ubuntu LTS |
| Web Server | Apache 2 |
| Framework | Laravel 13 |
| Database | PostgreSQL |
| Cache | Redis |
| Queue | Redis |
| Queue Dashboard | Laravel Horizon |
| Health Monitoring | Laravel Pulse |
| WebSockets | Laravel Reverb |
| Process Manager | Supervisor (Horizon + Reverb + Scheduler) |
| SSL | Let's Encrypt |
| Scheduler | Laravel Scheduler |

Apache should be configured with:

- HTTP/2
- Compression
- Browser Caching
- Keep-Alive
- Security Headers
- Optimized Virtual Hosts

---

# Technology Philosophy

Before introducing any dependency, ask:

1. Does Laravel already provide this functionality?
2. Is there a mature package maintained by Laravel or a trusted ecosystem partner?
3. Will this improve maintainability?
4. Does it integrate naturally with the existing architecture?
5. Does it simplify future AI-assisted development?

If the answer is uncertain, choose the simpler solution.


Now that we've committed to Redis + Reverb + Broadcasting, I would also make the entire application event-driven.

Instead of components calling one another directly:
we'll have:

Livewire
      │
      ▼
 Action
      │
      ▼
 Domain Event
      │
 ┌────┼───────────────┬───────────────┐
 ▼    ▼               ▼               ▼
Queue Notification Broadcast Activity Log
      │               │
      ▼               ▼
 Redis          Laravel Reverb
