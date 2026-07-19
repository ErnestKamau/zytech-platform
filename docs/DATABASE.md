# Zytech Contractors Platform

# 04_DATABASE.md

> Version: 1.0
> Database Engine: PostgreSQL 16+
> Cache: Redis
> ORM: Laravel Eloquent
> Last Updated: July 2026

---

# Table of Contents

1. Philosophy
2. Database Standards
3. Naming Conventions
4. PostgreSQL Standards
5. Primary Keys
6. UUID Policy
7. Relationships
8. Foreign Keys
9. Soft Deletes
10. Enum Strategy
11. JSONB Strategy
12. Audit Logging
13. Redis Strategy
14. Media Architecture
15. Domain Models
16. Migration Standards
17. Performance
18. Future Expansion

---

# Philosophy

The database is the single source of truth for the Zytech Contractors Platform.

Every feature in the application should be designed around a clean, normalized, scalable relational model.

The database must support:

- Marketing Website
- CMS
- Client Portal
- Realtime Features
- Mobile Applications
- ERP Modules
- Analytics
- API Integrations

The schema should never be designed solely for today's requirements.

Instead, every table should be capable of supporting future business processes.

---

# Guiding Principles

The database should be:

- Consistent
- Predictable
- Highly normalized
- Well indexed
- Self documenting
- Extensible

Avoid premature denormalization.

Denormalize only after profiling demonstrates a measurable performance benefit.

---

# Database Standards

## Database Engine

PostgreSQL is the official database.

Reasons:

- Excellent relational integrity
- Better indexing
- JSONB support
- Full Text Search
- GIS Extensions
- Materialized Views
- Generated Columns
- Mature ecosystem

---

## Character Encoding

UTF-8

Every database must use UTF-8 encoding.

---

## Timezone

All timestamps stored in UTC.

Presentation should always convert to the user's timezone.

---

## Naming Standards

Everything is lowercase.

Everything is snake_case.

Never abbreviate words.

Good

project_categories

quote_requests

project_timelines

Bad

proj_tbl

cat_tbl

usr_docs

---

# Table Naming

Plural

Examples

projects

services

users

documents

quote_requests

testimonials

knowledge_articles

---

# Pivot Tables

Alphabetical order.

Examples

project_service

project_tag

quotation_document

Never use generic names.

Bad

relations

links

pivot

---

# Column Naming

Foreign Keys

Singular

_id

Examples

project_id

client_id

service_id

quotation_id

Never

pid

projid

projectID

---

# Boolean Columns

Prefix with

is_

or

has_

Examples

is_active

is_featured

has_video

is_completed

---

# Date Columns

Use explicit names.

approved_at

published_at

completed_at

emailed_at

accepted_at

Avoid

date

timestamp

time

---

# Monetary Columns

Never use float.

Always use

numeric

or

decimal

Example

estimated_cost

quoted_amount

paid_amount

Currency stored separately when required.

---

# Text Columns

Short

string

Long

text

Very Large

longText

Use appropriately.

---

# JSON Columns

Use JSONB only when:

- Dynamic metadata
- Third-party payloads
- Flexible settings
- API responses

Never replace relational tables with JSON.

---

# Primary Key Strategy

The platform uses **UUID v7** as the primary key for every table.

This is a deliberate architectural decision made to support:

- Future APIs
- Mobile applications
- Distributed systems
- Offline synchronization
- Improved security
- Easier data migration
- Cross-system integrations
- ERP expansion

Laravel's native UUID support should be used throughout the application.

No table should use auto-incrementing integer IDs.

Example:

```php
$table->uuid('id')->primary();
```

Every Eloquent model should be configured accordingly.

```php
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Project extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;
}
```

---

# UUID Standard

The project standard is:

**UUID Version 7**

Reasons:

- Time ordered
- Better PostgreSQL indexing
- Excellent scalability
- Predictable insertion patterns
- Native Laravel support
- Globally unique
- API friendly

UUID v4 should not be used unless required by a third-party integration.

---

# Foreign Keys

All foreign keys should also use UUIDs.

Example:

```php
$table->foreignUuid('project_id')
      ->constrained()
      ->cascadeOnDelete();
```

Never mix integer and UUID relationships.

The entire schema should remain consistent.

---

# Public Resource Identifiers

The primary UUID serves as the public identifier.

Examples:

```
/projects/01984c1b-f95b-78df-a3c7-d46a9d1d12af

/services/01984c22-3d45-70be-a0f8-10cb30dbe012

/quotes/01984c3d-a01d-74de-8a0f-55c7f6df42de
```

No secondary public identifier is required.

---

# URL Strategy

Every resource exposed publicly should use route model binding with UUIDs.

Example:

```php
Route::get('/projects/{project}', ProjectPage::class);
```

Laravel should resolve the model using its UUID automatically.

Numeric identifiers must never be exposed in URLs.

---

# UUID Generation

UUID generation should be delegated to Laravel.

Do not manually generate UUIDs unless there is a specific business requirement.

Use:

- `HasUuids` trait
- `foreignUuid()` in migrations
- Route model binding

Avoid custom UUID implementations.

---

# Benefits of UUID Everywhere

Using UUIDs consistently provides several long-term advantages:

| Benefit | Description |
|----------|-------------|
| Security | Prevents enumeration attacks by avoiding sequential IDs. |
| Scalability | Supports distributed systems without ID collisions. |
| API Readiness | Stable identifiers across services and clients. |
| Offline Sync | Mobile applications can generate IDs before synchronization. |
| Data Migration | Easier merging of data from multiple environments. |
| Future ERP | Modules can be developed independently while maintaining globally unique references. |
| Consistency | A single identifier strategy simplifies development and debugging. |

---

# Development Rules

Every new migration must:

- Use UUID primary keys.
- Use UUID foreign keys.
- Define foreign key constraints.
- Create supporting indexes where appropriate.

Example:

```php
Schema::create('projects', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->foreignUuid('client_id')
          ->nullable()
          ->constrained()
          ->nullOnDelete();

    $table->string('title');

    $table->timestamps();

    $table->softDeletes();
});
```

No migration should introduce an auto-incrementing integer ID unless there is an exceptional, documented reason approved through an Architecture Decision Record (ADR).

---

# Guiding Principle

The database should have **one identifier strategy**.

Every table, relationship, API endpoint, event payload, notification, and future integration should rely on UUIDs to maintain consistency across the entire platform.

---

# Relationship Strategy

The platform follows a **strict relational model**.

Relationships should express business rules, not implementation convenience.

Every relationship must be explicit, predictable, and enforced at the database level.

Whenever possible:

- Use foreign key constraints.
- Index foreign keys.
- Define cascading behaviour.
- Document relationship ownership.
- Avoid orphaned records.

---

# Relationship Types

The platform primarily uses the following relationship patterns.

| Relationship | Usage |
|-------------|-------|
| One-to-One | Company Settings, User Profile |
| One-to-Many | Projects → Images |
| Many-to-Many | Projects ↔ Services |
| Polymorphic | Media, Activity Logs, Comments (Future) |
| Self-Referencing | Knowledge Centre Categories (Future) |

---

# One-to-One

Use only when two entities always have a single corresponding record.

Examples

```
User
 └── Profile
```

```
Company
 └── Settings
```

Avoid unnecessary one-to-one tables.

---

# One-to-Many

The most common relationship.

Example

```
Project
    ├── Images
    ├── Videos
    ├── Timeline Events
    ├── Documents
    └── Testimonials
```

Parent owns lifecycle.

Deleting the parent should follow the configured cascade policy.

---

# Many-to-Many

Used where entities naturally relate to multiple records.

Examples

Projects ↔ Services

Projects ↔ Tags

Knowledge Articles ↔ Tags

Users ↔ Roles

Roles ↔ Permissions

Pivot tables should remain lightweight.

Avoid storing business logic inside pivot tables unless necessary.

---

# Polymorphic Relationships

Polymorphism should be used sparingly.

Approved uses:

- Media
- Activity Logs
- Future Comments
- Future Attachments

Avoid polymorphism where a standard relationship is sufficient.

---

# Foreign Key Policy

Every relationship should be enforced using foreign keys.

Example

```php
$table->foreignUuid('client_id')
    ->nullable()
    ->constrained()
    ->nullOnDelete();
```

Never leave relationships unenforced.

---

# Cascade Rules

Cascade behaviour must reflect business rules.

| Scenario | Strategy |
|----------|----------|
| Project deleted | Delete gallery media (logical delete where appropriate) |
| User deleted | Restrict if ownership exists |
| Client deleted | Set NULL where historical data must remain |
| Quote deleted | Restrict if converted to project |
| Service deleted | Restrict while attached to projects |

Choose the safest option.

Never delete historical business data accidentally.

---

# Soft Delete Policy

Soft deletes are not the default.

They should be used only where business history is valuable.

Recommended:

✓ Projects

✓ Clients

✓ Quotations

✓ Documents

✓ Knowledge Articles

✓ Team Members

Avoid soft deletes for:

- Pivot tables
- Temporary records
- Cache tables
- Queue tables
- Sessions

---

# Enum Strategy

Application state should never rely on magic strings.

Use PHP Enums for all finite state values.

Examples:

ProjectStatus

```php
enum ProjectStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
```

QuotationStatus

```php
enum QuotationStatus: string
{
    case Pending = 'pending';
    case Reviewing = 'reviewing';
    case Sent = 'sent';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Completed = 'completed';
}
```

Role names should remain compatible with Spatie Permission.

---

# Database Enum Policy

Database enum columns should be used selectively.

Preferred approach:

- PHP Enums
- String columns
- Validation Rules

Database enums should only be introduced where schema-level enforcement is required.

This provides easier deployment and versioning.

---

# JSON / JSONB Strategy

PostgreSQL JSONB is a powerful feature but should be used intentionally.

JSONB is appropriate for:

- Dynamic metadata
- External API payloads
- SEO metadata
- Flexible configuration
- Analytics payloads

Never replace relational design with JSON.

Bad

```
project_data

{
    images: [],
    services: [],
    client: {}
}
```

Good

Projects

↓

Images

Services

Client

Relations

---

# Approved JSONB Fields

Examples

settings

seo

social_links

metadata

analytics_payload

search_index

Third-party webhooks

Avoid storing core business data in JSON.

---

# Redis Data Strategy

Redis complements PostgreSQL.

Redis never replaces PostgreSQL.

Source of Truth

↓

PostgreSQL

↓

Redis Cache

---

# Cached Data

Recommended cache candidates:

- Company Settings
- Homepage Content
- Navigation
- Featured Projects
- Featured Services
- Testimonials
- FAQs
- Knowledge Categories
- Statistics
- Hero Section

---

# Never Cache

Never cache:

- Authorization decisions
- User permissions
- Sensitive client data
- Draft quotations
- Session-specific business data

---

# Cache Lifetime

Recommended defaults.

| Data | TTL |
|------|-----|
| Settings | Forever (invalidate on update) |
| Navigation | Forever |
| Homepage | 30 minutes |
| Projects | 30 minutes |
| Services | 1 hour |
| Testimonials | 1 hour |
| Blog Articles | 1 hour |

Invalidate caches automatically after CRUD operations.

---

# Cache Naming Convention

Always namespace cache keys.

Examples

```
settings.company

settings.social

homepage.hero

homepage.statistics

projects.featured

projects.latest

services.all

knowledge.featured
```

Avoid anonymous cache keys.

---

# Database Constraints

Every table should enforce:

✓ Primary Key

✓ Foreign Keys

✓ Unique Constraints

✓ Check Constraints (where appropriate)

✓ NOT NULL where possible

Avoid nullable columns unless the business process genuinely allows missing data.

---

# Unique Constraints

Examples

User Email

Company Registration Number

Project Slug

Knowledge Article Slug

Quote Reference

Document UUID

Every unique business identifier should be protected by a database constraint.

---

# Check Constraints

Where appropriate, use PostgreSQL check constraints.

Examples

Estimated Cost >= 0

Completion Date >= Start Date

Percentage Complete BETWEEN 0 AND 100

These constraints complement—not replace—application validation.

---

# Data Integrity

The database should reject invalid data before it reaches the application.

Application validation is important.

Database validation is essential.

Both should exist.

Never rely solely on frontend validation.

---

# Guiding Principle

Relationships should describe the business.

Constraints should protect the business.

The database should actively enforce correctness rather than assuming every application layer behaves perfectly.

---

# Domain Model

The platform is organized around business domains rather than pages or controllers.

Each domain encapsulates:

- Business rules
- Database tables
- Relationships
- Events
- Policies
- Media
- Notifications
- Future expansion

This keeps the application modular and easy to evolve.

---

# Domain Overview

```text
Website
│
├── Company
├── Projects
├── Services
├── Quotations
├── Clients
├── Knowledge Centre
├── Media
├── Documents
├── Team
├── Testimonials
├── Users
├── Notifications
├── Settings
└── Activity
```

Each domain should be independently maintainable.

---

# Company Domain

Responsible for:

- Company Profile
- Mission
- Vision
- Contact Information
- Social Media
- Offices
- Business Registration
- Company Statistics

## Tables

company_settings

social_links

offices *(future)*

---

# Relationships

Company

↓

Social Links

↓

Office Locations

---

# Events

CompanySettingsUpdated

CompanyProfileUpdated

---

# Cache

settings.company

settings.social

settings.contact

---

# Future Expansion

- Multiple Branches
- Regional Offices
- Multi-company Support

---

# Projects Domain

The Projects domain is the heart of the marketing website.

It showcases completed work and demonstrates company capability.

---

## Responsibilities

- Portfolio
- Galleries
- Videos
- Documents
- Construction Timeline
- Project Categories
- Featured Projects
- Related Services
- SEO

---

## Tables

projects

project_categories

project_timelines

project_locations

project_tags

project_service

---

## Relationships

```text
Project
│
├── Category
├── Timeline
├── Services
├── Documents
├── Media
├── Tags
├── Testimonials
└── Location
```

---

## Events

ProjectCreated

ProjectUpdated

ProjectPublished

ProjectArchived

ProjectFeatured

MediaAttached

TimelineUpdated

---

## Cache

projects.latest

projects.featured

projects.statistics

projects.map

---

## Future Expansion

- Progress Tracking
- Project Budget
- Site Reports
- Site Diary
- Staff Assignment
- Equipment Tracking

---

# Services Domain

Represents every service offered by the company.

Examples

- Interior Design
- Exterior Design
- Property Sketching
- Plan Estimation
- Plan Approvals

---

## Tables

services

service_categories *(future)*

---

## Relationships

```text
Service

↓

Projects

↓

Quotations
```

---

## Events

ServiceCreated

ServiceUpdated

ServicePublished

---

## Cache

services.all

services.featured

---

# Quotations Domain

This domain powers the quotation request lifecycle.

---

## Responsibilities

- Request Quote
- Admin Review
- Customer Communication
- Attachments
- Status Tracking
- Approval Workflow

---

## Tables

quote_requests

quotations

quotation_documents

quotation_messages

---

## Status Flow

```text
Pending

↓

Reviewing

↓

Preparing

↓

Sent

↓

Accepted
 │
 ├────────────► Converted to Project
 │
Rejected
```

---

## Events

QuoteRequested

QuoteReviewed

QuotePrepared

QuoteSent

QuoteAccepted

QuoteRejected

QuoteConverted

---

## Cache

None

Business data should always be fresh.

---

## Future Expansion

- Digital Signatures
- Online Payments
- Auto-generated BOQs
- Client Portal
- ERP Integration

---

# Clients Domain

Represents authenticated and prospective customers.

---

## Responsibilities

- Portal Access
- Quotations
- Documents
- Communication
- Notifications

---

## Tables

clients

client_profiles

---

## Relationships

Client

↓

Quotations

↓

Projects

↓

Documents

---

## Events

ClientRegistered

ClientInvited

ClientActivated

---

# Knowledge Centre

The platform's SEO engine.

---

## Responsibilities

Educational content

Construction guides

Planning advice

Industry updates

SEO

---

## Tables

knowledge_articles

knowledge_categories

knowledge_tags

---

## Events

ArticlePublished

ArticleArchived

---

## Cache

knowledge.featured

knowledge.categories

---

# Media Domain

Centralized media management using Spatie Media Library.

Every upload belongs to exactly one owning model.

No standalone media records should exist.

Supported media:

- Images
- Videos
- PDFs
- Blueprints
- CAD Drawings
- Contracts
- Certificates

Media conversions should be queued.

---

# Team Domain

Represents company staff shown on the website.

Tables

team_members

---

Events

TeamMemberAdded

TeamMemberUpdated

---

# Testimonials Domain

Stores customer feedback.

Tables

testimonials

---

Events

TestimonialPublished

TestimonialArchived

---

# Notifications Domain

Uses Laravel Notifications.

Delivery channels

- Database
- Broadcast
- Email

Future

SMS

Push Notifications

WhatsApp

---

# Settings Domain

Uses Spatie Laravel Settings.

Configuration stored in the database rather than `.env` whenever appropriate.

Examples

SEO

Company

Homepage

Contact

Social

Analytics

---

# Activity Domain

Uses Spatie Activity Log.

Tracks all significant business events.

Examples

Quote Updated

Project Published

Media Uploaded

User Invited

Settings Changed

No custom audit logging implementation should be created unless absolutely necessary.

---

# Observability Tables

## Pulse

Laravel Pulse stores application health metrics in dedicated PostgreSQL tables created by its package migrations.

Pulse data is operational telemetry — not business domain data.

Do not model Pulse tables inside domain modules.

## Telescope

Telescope tables (local) store debugging entries. Keep Telescope disabled or tightly gated outside local development.

## Horizon

Horizon uses Redis for its metrics and job metadata. No Horizon business tables are required in PostgreSQL.

---

# Domain Independence

Domains should communicate through:

- Events
- Actions
- Interfaces

Never through direct coupling.

This enables:

- Easier testing
- Better maintainability
- Independent evolution
- Future microservice extraction if ever required

---

# Guiding Principle

Think in business capabilities—not database tables.

A well-designed domain model makes every other architectural decision easier.

---

# Database Performance Philosophy

Performance is a design requirement—not an optimization performed after deployment.

Every schema, relationship, query, and index should be designed with scalability in mind.

The database should comfortably support:

- Thousands of projects
- Hundreds of thousands of media records
- Millions of activity log entries
- Millions of notifications
- Large document libraries
- Future ERP modules

Performance decisions should always prioritize long-term maintainability over premature optimization.

---

# PostgreSQL Best Practices

The platform follows PostgreSQL-first development.

Leverage PostgreSQL's strengths:

- UUID v7
- JSONB
- GIN Indexes
- Partial Indexes
- Expression Indexes
- Materialized Views (Future)
- Full Text Search
- Generated Columns (when beneficial)

Avoid designing for MySQL compatibility.

---

# Indexing Strategy

Indexes are mandatory for columns frequently used in:

- WHERE
- ORDER BY
- JOIN
- GROUP BY
- DISTINCT
- Foreign Keys

Every migration introducing searchable data should evaluate indexing requirements.

---

# Standard Indexes

Every table should include indexes for:

✓ Primary Key

✓ Foreign Keys

✓ Slugs

✓ Published Status

✓ Active Status

✓ Created Date (when sorting)

✓ Updated Date (when sorting)

---

# Composite Indexes

Use composite indexes for common query combinations.

Example

Projects

```sql
(status, published_at)

(category_id, status)

(featured, published_at)
```

Knowledge Articles

```sql
(category_id, published_at)

(status, published_at)
```

Quote Requests

```sql
(status, created_at)

(client_id, status)
```

Composite indexes should match actual query patterns.

Never create unnecessary indexes.

---

# Unique Indexes

Protect business uniqueness.

Examples

```text
users.email

projects.slug

services.slug

knowledge_articles.slug

roles.name

permissions.name
```

Use unique constraints instead of relying solely on validation.

---

# JSONB Indexing

When querying JSONB fields frequently, create GIN indexes.

Example

```sql
CREATE INDEX idx_project_metadata
ON projects
USING GIN(metadata);
```

Never query large JSONB documents without appropriate indexing.

---

# Foreign Key Indexes

Every foreign UUID should be indexed.

Laravel typically creates these automatically when using:

```php
$table->foreignUuid('project_id')
      ->constrained();
```

Always verify generated indexes during reviews.

---

# Soft Delete Indexes

Tables using Soft Deletes should index:

```text
deleted_at
```

This improves performance for the default:

```php
Model::query()
```

which automatically excludes soft-deleted records.

---

# Query Standards

Every query should be intentional.

Prefer expressive Eloquent over raw SQL where possible.

---

# Avoid N+1 Queries

Always eager load relationships.

Bad

```php
Project::all();

foreach ($projects as $project) {
    $project->media;
}
```

Good

```php
Project::with('media')->get();
```

---

# Limit Selected Columns

Do not select unnecessary data.

Bad

```php
Project::all();
```

Good

```php
Project::select([
    'id',
    'title',
    'slug',
    'featured_image',
])->get();
```

---

# Pagination

Never load large datasets into memory.

Use:

```php
paginate()

simplePaginate()

cursorPaginate()
```

Cursor pagination is preferred for large datasets.

---

# Chunking

Large operations should use:

```php
chunk()

chunkById()

lazy()

cursor()
```

Avoid processing thousands of records in memory.

---

# Query Scopes

Frequently reused conditions belong in local scopes.

Example

```php
Project::published()
       ->featured()
       ->latest()
```

Avoid duplicating query logic.

---

# Query Objects

Complex queries should be extracted into dedicated Query classes.

Example

```text
Domains/

Project/

Queries/

FeaturedProjectsQuery.php
```

Avoid placing large query chains inside Livewire components or controllers.

---

# Raw SQL

Raw SQL is permitted when it provides measurable benefits.

Examples:

- Reporting
- Analytics
- Materialized Views
- Recursive Queries
- Bulk Updates

Document why raw SQL is used.

---

# Materialized Views

Future ERP modules may introduce PostgreSQL Materialized Views.

Possible candidates:

- Dashboard Statistics
- Financial Reports
- Construction Metrics
- Company Analytics

Materialized Views should be refreshed through queued jobs.

---

# Full Text Search

The initial release uses PostgreSQL queries.

Future implementation:

Laravel Scout

↓

Meilisearch

↓

Hybrid PostgreSQL Search

Avoid building custom search engines.

---

# Caching Strategy

Redis should cache expensive read operations.

Examples

Homepage

Featured Projects

Homepage Statistics

Service Lists

Knowledge Categories

Testimonials

Navigation

Company Settings

Never cache mutable business transactions.

---

# Cache Invalidation

Caches must be invalidated automatically through domain events.

Example

```text
ProjectPublished

↓

Clear Featured Projects Cache

↓

Refresh Homepage Cache

↓

Broadcast Update

↓

Rebuild Search Index
```

Never manually clear caches from controllers.

---

# Repository Pattern

The Repository Pattern is **not** the default architecture.

Laravel Eloquent already provides an expressive repository abstraction.

Repositories should only be introduced when:

- Integrating external systems
- Aggregating multiple data sources
- Swapping persistence mechanisms

Avoid creating repositories that merely wrap Eloquent.

---

# Transactions

Database transactions should be used whenever multiple operations must succeed or fail together.

Example

```php
DB::transaction(function () {
    // Create quotation
    // Attach documents
    // Dispatch event
});
```

Never leave the database in a partially updated state.

---

# Locks

Use Redis-backed atomic locks for operations requiring exclusive access.

Examples

- Duplicate quote prevention
- Scheduled imports
- Long-running synchronizations

Prefer Laravel's cache lock API.

---

# Performance Checklist

Before merging a feature, verify:

- Queries are indexed.
- Relationships are eager loaded.
- Pagination is used.
- Caching is appropriate.
- Transactions protect multi-step writes.
- Queue heavy tasks.
- Query count is reasonable.
- Response time is acceptable.

Performance is everyone's responsibility.

---

# Guiding Principle

Optimize based on evidence, not assumptions.

Measure first.

Improve second.

Document third.

---
---

# Database Blueprint

This section defines every database entity that exists within the Zytech Contractors Platform.

The goal is not simply to list tables but to document the ownership, purpose, relationships, and lifecycle of every domain object.

Every entity should belong to exactly one domain.

---

# Company Domain

## company_settings

Stores global application and company configuration.

### Responsibilities

- Company Information
- Contact Information
- Business Registration
- Mission & Vision
- Homepage Statistics
- Default SEO
- Branding
- Business Hours

### Relationships

```
Company Settings

↓

Social Links

↓

Office Locations (Future)
```

### Cached

✓ Yes

Redis Key

```
settings.company
```

---

## social_links

Stores official company social media accounts.

Supported Platforms

- Facebook
- Instagram
- LinkedIn
- X
- TikTok
- YouTube

---

# Project Domain

## projects

The primary portfolio entity.

Represents completed, ongoing, or featured construction projects.

---

### Core Fields

- UUID
- Title
- Slug
- Summary
- Description
- Status
- Featured
- Completion Date
- Location
- SEO Metadata

---

### Relationships

```
Project

├── Category
├── Services
├── Timeline
├── Media
├── Documents
├── Testimonials
├── Tags
└── Location
```

---

### Events

ProjectCreated

ProjectUpdated

ProjectPublished

ProjectArchived

ProjectFeatured

---

### Cache

projects.featured

projects.latest

projects.statistics

---

## project_categories

Categorizes projects.

Examples

Residential

Commercial

Industrial

Hospitality

Infrastructure

Government

Educational

Healthcare

---

## project_timelines

Represents milestones within a construction project.

Examples

- Planning
- Site Preparation
- Foundation
- Structural Works
- Roofing
- Finishes
- Landscaping
- Handover

Each milestone may include:

- Date
- Description
- Images
- Documents

---

## project_locations

Stores project geographical information.

Supports:

- County
- Town
- GPS Coordinates
- Google Maps Link

Future

PostGIS integration.

---

## project_tags

Optional tagging.

Examples

Luxury

Eco-Friendly

Renovation

Apartment

Villa

Office

Warehouse

---

## project_service

Many-to-many relationship.

Projects

↔

Services

---

# Service Domain

## services

Stores company services.

Examples

- Interior Design
- Exterior Design
- Property Sketching
- Plan Estimation
- Plan Approvals

---

### Relationships

```
Service

↓

Projects

↓

Quote Requests
```

---

# Quotation Domain

## quote_requests

Public quote requests.

Submitted through:

Website

Client Portal

Future Mobile App

---

### Status

Pending

↓

Review

↓

Preparing

↓

Sent

↓

Accepted

↓

Converted to Project

---

## quotations

Official quotations issued by Zytech.

Contains:

- Pricing
- Notes
- Terms
- Expiry
- Attachments

---

## quotation_documents

Stores quotation attachments.

Examples

PDF

Drawings

BOQ

Contracts

Specifications

---

## quotation_messages

Conversation between client and company.

Future

Realtime messaging using Reverb.

---

# Client Domain

## clients

Represents authenticated customers.

A client may have:

- Many quotations
- Many projects
- Many notifications
- Many documents

---

## client_profiles

Additional client information.

Examples

Company

Phone

Address

Preferred Communication

Tax Information

Future

Billing Profiles

---

# Knowledge Centre

## knowledge_articles

Educational articles.

Examples

Building Costs

Construction Tips

Permit Guides

Planning Advice

---

## knowledge_categories

Examples

Architecture

Construction

Approvals

Interior Design

Landscaping

Planning

---

## knowledge_tags

Flexible tagging.

---

# Team Domain

## team_members

Staff displayed publicly.

Examples

Architect

Engineer

Project Manager

Quantity Surveyor

Interior Designer

---

# Testimonials Domain

## testimonials

Stores customer feedback.

Testimonials may optionally belong to:

Projects

Services

Clients

---

# Media Domain

Uses:

Spatie Media Library

No custom media tables should be introduced.

Collections include:

project-images

project-videos

documents

team

logos

hero

knowledge

testimonials

future-blueprints

future-cad-files

---

# Notification Domain

Laravel Notifications.

Channels

Database

Broadcast

Mail

Future

SMS

WhatsApp

Push Notifications

---

# User Domain

Uses Laravel Users.

Enhanced by:

Spatie Permission

Roles

Permissions

Activity Log

---

# Roles

Administrator

Director

Project Manager

Architect

Engineer

Sales

Client

Future

Accountant

Site Supervisor

Procurement Officer

HR

---

# Permission Strategy

Permission names should remain human-readable.

Examples

projects.view

projects.create

projects.update

projects.delete

quotes.approve

quotes.send

users.invite

settings.manage

Avoid abbreviated permission names.

---

# Settings Domain

Uses:

Spatie Laravel Settings

Configuration Groups

Company

Homepage

SEO

Analytics

Contact

Email

Social

Branding

Future

CRM

ERP

Finance

---

# Activity Domain

Uses:

Spatie Activity Log

Track:

Projects

Quotes

Users

Roles

Settings

Permissions

Documents

Media

Clients

Authentication

---

# Future ERP Domains

Reserved domains.

Inventory

Procurement

Accounting

Payroll

HR

Fleet

Equipment

Suppliers

Invoices

Purchase Orders

Site Reports

Site Inspections

Safety

Maintenance

Assets

Warehouse

These domains should remain independent and communicate through Events.
