# Zytech Contractors Platform

# 08_FILAMENT.md

> Version: 1.0
> Status: Living Document
> Project: Zytech Contractors Digital Platform
> Last Updated: July 2026

---

# Table of Contents

1. Purpose
2. Filament Philosophy
3. Panel Architecture
4. Navigation Structure
5. Dashboard
6. Resource Architecture
7. Form Standards
8. Table Standards
9. Widgets
10. Media Management
11. User Management
12. Roles & Permissions
13. Notifications
14. Search
15. Activity Logging
16. Settings
17. Realtime Features
18. Performance
19. Security
20. Engineering Standards
21. Appendices

---

# Purpose

The Filament Administration Panel is the operational backbone of the Zytech Contractors Platform.

Its purpose is not simply to provide CRUD interfaces.

It should enable staff to efficiently manage projects, quotations, clients, company content, and business operations while maintaining the same quality standards reflected on the public website.

Every administrative workflow should reduce friction, improve productivity, and ensure data integrity.

---

# Filament Philosophy

The administration experience should feel like a modern SaaS application.

Core principles:

- Fast
- Responsive
- Professional
- Intuitive
- Permission Driven
- Event Driven
- Keyboard Friendly
- Mobile Responsive
- Accessible

Administration should feel effortless.

---

# Administration Goals

The admin panel should:

- Reduce repetitive work
- Minimize clicks
- Encourage consistency
- Prevent mistakes
- Support collaboration
- Scale as the company grows
- Support future ERP modules

---

# Panel Architecture

The platform will use a single Filament Panel during Version 1.

```
Admin Panel

│

├── Dashboard

├── Company

├── Services

├── Projects

├── Quotations

├── Clients

├── Knowledge Centre

├── Media Library

├── Users

├── Settings

└── System
```

Future releases may introduce:

- Client Panel
- Vendor Panel
- Procurement Panel
- Finance Panel

---

# Navigation Philosophy

Navigation should reflect the business—not the database.

Avoid technical names.

Preferred

Projects

Quotations

Clients

Services

Articles

Media

Settings

Avoid

Project Categories

Project Images

Quote Items

Quote Attachments

Those belong inside resources.

---

# Navigation Groups

## Dashboard

Overview

---

## Company

Company Profile

Team

Statistics

Certifications

Testimonials

---

## Portfolio

Projects

Categories

Locations

Tags

---

## Services

Services

FAQs

Benefits

Processes

---

## Sales

Quote Requests

Quotations

Clients

Documents

---

## Content

Knowledge Centre

Categories

Tags

Media

---

## Administration

Users

Roles

Permissions

Activity Logs

Notifications

Settings

System Health

---

# Dashboard Philosophy

The dashboard should answer one question:

> "How is the business performing today?"

Avoid overwhelming users.

Surface only actionable information.

---

# Dashboard Widgets

Executive Summary

- New Quote Requests
- Pending Quotations
- Active Clients
- Featured Projects
- Recent Articles

Business Metrics

- Quote Conversion Rate
- Monthly Enquiries
- Published Projects
- Active Services

Operational Metrics

- Draft Content
- Failed Jobs
- Queue Status
- Cache Status

Realtime Feed

- Recent Activity
- New Messages
- Notifications

---

# Widget Standards

Every widget should:

- Load independently
- Be cache-aware
- Refresh automatically
- Support polling or Reverb
- Handle empty states
- Display loading placeholders

---

# Dashboard Layout

```
Header

↓

Quick Actions

↓

Executive Metrics

↓

Charts

↓

Latest Activity

↓

Notifications

↓

Recent Items
```

Users should understand system health within 10 seconds.

---

# Quick Actions

Provide one-click access to common tasks.

Examples:

- New Project
- New Service
- New Quote
- Upload Media
- Publish Article
- Invite Client

Quick Actions should be role-aware.

---

# Dashboard Personalization

Future versions should allow users to:

- Rearrange widgets
- Hide widgets
- Save layouts
- Pin shortcuts

Personalization should never affect other users.

---

# Global Search

The search bar should be available throughout the panel.

Searchable entities:

- Projects
- Services
- Clients
- Quotations
- Articles
- Users
- Media

Search results should be permission-aware.

---

# Breadcrumbs

Every resource should generate breadcrumbs automatically.

Example:

Dashboard

↓

Projects

↓

Residential Villa

↓

Edit

---

# UUID Routing

Every Filament Resource must use UUIDs for route model binding.

Never expose numeric identifiers.

URLs should remain stable and opaque.

Example:

```
/admin/projects/550e8400-e29b-41d4-a716-446655440000/edit
```

---

# Empty States

Every empty resource should provide:

- Illustration
- Helpful explanation
- Primary action
- Optional documentation link

Example:

"No projects have been created yet."

↓

Create First Project

---

# Keyboard Navigation

Support keyboard-first workflows.

Examples:

- Search shortcut
- Escape to close modals
- Tab navigation
- Arrow key navigation where appropriate

---

# Accessibility

Target WCAG 2.2 AA.

Requirements:

- Keyboard support
- Focus management
- Accessible forms
- Semantic HTML
- Contrast compliance
- Reduced motion

---

# Guiding Principle

The Filament panel should feel less like an administration system and more like professional business software.

Every interaction should save time, reduce mistakes, and improve operational efficiency.

---

---

# Resource Architecture

## Philosophy

Every Filament Resource represents a business capability—not merely a database table.

Resources should encapsulate all administrative functionality required to manage a business entity while maintaining consistency across the platform.

A resource should be:

- Discoverable
- Consistent
- Permission-aware
- Event-driven
- Accessible
- Extensible
- Testable

The user should never have to "learn" a new resource. Every resource should behave consistently.

---

# Resource Blueprint

Every Filament Resource must follow the same architecture.

```
Resource

│

├── Business Purpose

├── Navigation

├── Form

├── Table

├── Infolist

├── Widgets

├── Filters

├── Actions

├── Bulk Actions

├── Relation Managers

├── Permissions

├── Policies

├── Events

├── Notifications

├── Cache Invalidation

├── Audit Trail

└── Tests
```

Every resource should implement this blueprint unless there is a compelling business reason not to.

---

# Resource Directory Structure

```text
app/

└── Filament/

    ├── Resources/

    │

    ├── Company/

    ├── Projects/

    ├── Services/

    ├── Quotations/

    ├── Clients/

    ├── Articles/

    ├── Users/

    ├── Settings/

    └── Shared/
```

Shared form components should never be duplicated.

---

# Resource Naming Standards

Resources should use business terminology.

Examples

✅ ProjectResource

✅ QuoteResource

✅ ClientResource

✅ ServiceResource

Avoid

❌ TblProjectResource

❌ ProjectModelResource

❌ ProjectCrud

Resources describe business capabilities.

---

# Resource Responsibilities

A resource owns:

- Navigation
- Forms
- Tables
- Infolists
- Filters
- Actions
- Bulk Actions
- Relation Managers

A resource does **not** own:

- Business logic
- Validation rules shared across the domain
- Authorization policies
- Domain services
- Event listeners

Those belong elsewhere in the application architecture.

---

# Resource Lifecycle

Every resource should support the complete lifecycle of its entity.

```
Create

↓

Review

↓

Publish

↓

Update

↓

Archive

↓

Restore

↓

Delete (Rare)
```

Deletion should be exceptional.

Archiving should be preferred whenever possible.

---

# Resource States

Resources should visibly communicate state.

Examples

Draft

Published

Archived

Featured

Pending

Approved

Rejected

Completed

Cancelled

Each state should have:

- Badge
- Color
- Icon
- Filter
- Searchability

---

# UUID Strategy

Every resource must use UUIDs.

Rules

- Route Model Binding
- Foreign Keys
- API Responses
- Notifications
- Broadcast Events
- Activity Logs

Never expose incremental IDs.

---

# Resource Navigation

Navigation should include:

- Label
- Icon
- Badge (where appropriate)
- Group
- Sort Order

Navigation badges should display meaningful operational information.

Examples

Pending Quotes

Draft Articles

Unread Messages

Failed Jobs

Never display decorative badges.

---

# Resource Pages

Every resource should implement only the pages it genuinely requires.

Common pages

- List
- Create
- Edit
- View

Optional pages

- Import
- Export
- Analytics
- History
- Preview

Avoid unnecessary CRUD pages.

---

# Resource URLs

Follow predictable patterns.

```
/admin/projects

/admin/projects/create

/admin/projects/{uuid}

/admin/projects/{uuid}/edit
```

Consistency improves usability.

---

# Navigation Visibility

Resources should appear only when users have permission.

Do not hide actions within visible resources unnecessarily.

Hide the resource itself if access is not permitted.

---

# Global Search

Resources should define:

Search Title

Search Description

Search Keywords

Search Icon

Search Result Actions

Search should prioritize:

Title

Reference Number

Client

Location

Category

Avoid searching every field.

---

# Resource Metadata

Every resource should expose metadata where appropriate.

Examples

Created By

Updated By

Published By

Published At

Archived At

Last Viewed

Revision Count

Metadata supports auditing and transparency.

---

# Soft Deletes

Soft deletes should be enabled by default.

Hard deletes require elevated permissions and should be rare.

---

# Revision Awareness

Where appropriate, resources should expose revision history.

Examples

Projects

Services

Articles

Company Profile

Users should be able to understand:

- What changed
- Who changed it
- When it changed

---

# Activity Logging

Every significant action should generate an activity log.

Examples

Created

Updated

Published

Archived

Restored

Deleted

Media Uploaded

Quote Sent

Permission Changed

Activity logs should be human-readable.

Example:

"John published the Riverside Apartments project."

Avoid technical log messages.

---

# Resource Performance

Resources should remain responsive regardless of dataset size.

Guidelines

- Paginate large datasets
- Lazy load heavy relationships
- Use eager loading where appropriate
- Cache reference data
- Optimize queries
- Avoid N+1 queries

Large resources should continue to perform efficiently with tens of thousands of records.

---

# Resource Accessibility

Every resource must support:

- Keyboard navigation
- Screen readers
- Focus indicators
- Accessible forms
- Semantic labels
- Reduced motion preferences

Accessibility is a release requirement.

---

# Form Standards

## Philosophy

Forms are conversations with users.

Every form should be easy to complete, forgiving of mistakes, and optimized for speed.

Users should never feel overwhelmed.

---

# Form Layout

Recommended structure

```
Header

↓

Primary Information

↓

Secondary Information

↓

Media

↓

Relationships

↓

Publishing

↓

Metadata

↓

Actions
```

Complex forms should be divided into sections or tabs.

---

# Form Components

Supported components include:

- Text Input
- Textarea
- Rich Editor
- Markdown Editor
- Select
- Multi Select
- Toggle
- Checkbox
- Radio
- Date Picker
- Time Picker
- DateTime Picker
- Color Picker
- File Upload
- Image Upload
- Repeater
- Builder
- Key Value
- Tags Input
- Placeholder
- View Field

Use the simplest component that satisfies the requirement.

---

# Form Sections

Group related fields together.

Examples

Project Information

↓

Location

↓

Media

↓

Timeline

↓

SEO

↓

Publishing

Sections improve usability and maintainability.

---

# Validation

Validation should be:

- Immediate where appropriate
- Clear
- Actionable
- Consistent

Error messages should explain how to resolve the issue.

Avoid technical terminology.

---

# Autosave

Where appropriate, forms should support draft autosave.

Ideal candidates:

- Articles
- Company Profile
- Project Descriptions

Autosave should never overwrite intentional changes silently.

---

# Draft Support

Long-form content should support drafts.

Examples

Projects

Knowledge Articles

Company Information

Users should be able to resume work later.

---

# Media Integration

Forms should integrate seamlessly with the media library.

Support:

- Drag and Drop
- Multiple Uploads
- Image Cropping
- Responsive Images
- Video Attachments
- Document Attachments

Media should never block form completion.

---

# Form Actions

Common actions

Save

Save & Continue

Save Draft

Publish

Archive

Cancel

Delete (restricted)

Actions should remain consistent across all resources.

---

# Unsaved Changes

Warn users before navigating away when changes have not been saved.

Protect user input wherever possible.

---

# Definition of a Great Form

A great form is:

- Fast
- Understandable
- Accessible
- Forgiving
- Responsive
- Predictable
- Efficient
- Consistent

---

# Table Architecture

## Philosophy

Tables are the primary workspace for administrators.

Users should be able to quickly locate information, understand entity status, perform actions, and move on without unnecessary clicks.

Every table should prioritize:

- Readability
- Performance
- Consistency
- Accessibility
- Discoverability

A table is not simply a data grid.

It is an operational dashboard.

---

# Standard Table Layout

```
Header

↓

Title

↓

Description

↓

Header Actions

↓

Quick Filters

↓

Search

↓

Table

↓

Pagination

↓

Bulk Actions
```

The layout should remain consistent across every resource.

---

# Table Columns

Columns should display information in order of business importance.

Recommended order:

Primary Identifier

↓

Status

↓

Important Relationships

↓

Business Metadata

↓

Timestamps

↓

Actions

Never place timestamps before primary business information.

---

# Column Standards

Every column should:

- Have a descriptive label.
- Support sorting where appropriate.
- Support searching where appropriate.
- Truncate long content.
- Display tooltips for truncated values.
- Remain responsive.

---

# Column Types

Supported column types include:

- Text
- Badge
- Icon
- Image
- Avatar
- Boolean
- Date
- DateTime
- Money
- Percentage
- Progress
- Tags
- Relationship
- URL
- Email
- Phone
- Markdown Preview

Choose the simplest representation that communicates the information clearly.

---

# Status Badges

Status values should always be displayed using badges.

Examples

Draft

Published

Archived

Pending

Approved

Rejected

Completed

Cancelled

Each status should have:

- Color
- Icon
- Tooltip

Status colors should remain consistent throughout the platform.

---

# Sorting

Enable sorting only on meaningful columns.

Examples:

✓ Name

✓ Status

✓ Created Date

✓ Updated Date

✓ Published Date

Avoid sorting on large relationship datasets unless optimized.

---

# Searching

Search should prioritize:

- Name
- Reference Number
- UUID
- Client
- Service
- County
- Category

Avoid searching large text fields unless full-text search is implemented.

---

# Pagination

Default

25 records

Options

25

50

100

250

Remember user preference where appropriate.

---

# Sticky Elements

Support sticky:

- Table Header
- Bulk Actions
- Filters (optional)

This improves usability for large datasets.

---

# Responsive Tables

Desktop

Full Table

Tablet

Adaptive Columns

Mobile

Stacked Cards

Tables should never require horizontal scrolling on mobile devices.

---

# Empty Tables

Every empty table should include:

Illustration

Title

Helpful Description

Primary Action

Example

"No quotations found."

↓

Create Quotation

---

# Exporting

Supported formats:

CSV

Excel

PDF (future)

Exports should respect:

Permissions

Applied Filters

Search Queries

Visible Columns

---

# Importing

Supported formats:

CSV

Excel

Import process should include:

Validation

Preview

Conflict Detection

Rollback

Summary Report

Failed rows should never stop successful rows from importing.

---

# Saved Views

Future Feature

Users should be able to save:

Searches

Filters

Column Visibility

Sort Order

Pagination

Personal only.

---

# Table Performance

Large datasets should use:

Pagination

Optimized eager loading

Cursor pagination where appropriate

Indexes

Cached reference data

Tables should remain responsive even with tens of thousands of records.

---

# Table Accessibility

Requirements

Keyboard navigation

Focus indicators

Screen reader support

Semantic headers

Sortable announcements

Accessible pagination

---

# Table Completion Checklist

✓ Search

✓ Sort

✓ Filters

✓ Bulk Actions

✓ Empty State

✓ Loading State

✓ Responsive

✓ Accessible

✓ Tested

✓ Permission-aware

---

# Filters

## Philosophy

Filters help users answer business questions.

They should be meaningful, discoverable, and fast.

Avoid overwhelming users with dozens of filters.

---

# Common Filters

Status

Category

Service

Client

County

Project Type

Featured

Published

Archived

Created Date

Updated Date

Author

---

# Filter Groups

Filters should be grouped logically.

Example

Status

↓

Location

↓

Dates

↓

Relationships

↓

Metadata

---

# Date Filters

Support

Today

This Week

This Month

Last Month

This Quarter

This Year

Custom Range

---

# Relationship Filters

Support

Multi-select

Searchable

Lazy Loaded

Relationship filters should use optimized queries.

---

# Filter Persistence

Remember user filters between sessions where appropriate.

---

# Clear Filters

Always provide:

Clear All Filters

Reset View

---

# Filter Performance

Large relationship filters should:

Search asynchronously

Load lazily

Cache reference data

Never load thousands of options initially.

---

# Resource Actions

## Philosophy

Actions represent business operations.

Every action should clearly communicate:

What it does

Its consequences

Its permissions

Its feedback

---

# Standard Actions

View

Edit

Duplicate

Publish

Archive

Restore

Delete

Download

Preview

Share

---

# Action Placement

Primary actions

Top right

Secondary actions

Row actions

Destructive actions

Separated visually

Avoid mixing destructive actions with primary actions.

---

# Confirmation Dialogs

Require confirmation for:

Delete

Archive

Restore

Publish

Bulk Operations

Dialogs should explain consequences.

---

# Long Running Actions

Long-running operations should:

Dispatch queues

Show progress

Notify users when complete

Never block the interface.

---

# Notifications

Every successful action should provide feedback.

Example

"Project published successfully."

Avoid technical wording.

---

# Permission Awareness

Actions should only appear when:

User has permission.

Do not disable actions unnecessarily.

Hide unavailable actions.

---

# Audit Trail

Every significant action should create an activity log.

Examples

Published Project

Archived Service

Accepted Quotation

Uploaded Media

Assigned Role

---

# Relation Managers

## Philosophy

Relation Managers allow administrators to manage related business data without leaving the parent resource.

They should reduce navigation and improve workflow efficiency.

---

# Supported Relationships

Project

↓

Gallery

↓

Timeline

↓

Documents

↓

Videos

↓

Testimonials

Service

↓

FAQs

↓

Related Projects

Client

↓

Quotations

↓

Communications

↓

Documents

Article

↓

Tags

↓

Categories

↓

Related Articles

---

# Rules

Relation Managers should:

- Support CRUD where appropriate.
- Respect permissions.
- Lazy load data.
- Display counts.
- Support searching.
- Support pagination.

Avoid deeply nested relation managers.

---

# Inline Creation

Support inline creation where it improves workflow.

Examples

Add Project Image

Add Timeline Milestone

Add FAQ

Add Client Note

Avoid forcing users to leave the current context.

---

# Infolists

## Philosophy

Infolists present business information clearly without editing.

They are optimized for reading rather than modifying.

---

# Standard Sections

Primary Information

↓

Relationships

↓

Media

↓

Metadata

↓

Activity

---

# Supported Components

Text

Badge

Image

Video

Markdown

Rich Content

Timeline

Statistics

Key-Value

Repeatable Entries

---

# Metadata

Every infolist should expose:

Created By

Updated By

Published By

Created At

Updated At

UUID

Status

Revision Count

---

# Readability

Use section headings.

Avoid excessively long pages.

Collapse less important sections when appropriate.

Information should be easy to scan.

---

# Future Enhancements

Version History

Comparison View

Print View

PDF Export

Public Preview

---

# Project Resource

> The most important resource in the entire administration panel.

---

# Business Purpose

The Project Resource represents Zytech Contractors' completed, ongoing, and future construction projects.

It is far more than a portfolio.

It serves as:

- Marketing asset
- Sales tool
- Trust builder
- Knowledge source
- Client reference
- Company history

Projects are the company's strongest proof of capability.

---

# Business Goals

The Project Resource should allow staff to:

- Showcase completed work
- Manage project information
- Organize media
- Demonstrate expertise
- Improve SEO
- Support quotations
- Generate leads
- Build trust

---

# Navigation

Portfolio

↓

Projects

Icon

Heroicons Building Office

Navigation Badge

Display Draft Projects

Example

```
Draft (6)
```

Badge should update automatically.

---

# Project Lifecycle

```
Draft

↓

Internal Review

↓

Published

↓

Featured (Optional)

↓

Archived

↓

Restored

↓

Deleted (Rare)
```

Deletion should almost never occur.

---

# Form Architecture

## Section 1

Project Information

Fields

- Project Name
- Slug
- UUID
- Status
- Featured
- Completion Date
- Completion Year

---

## Section 2

Location

Fields

- Country

- County

- Town

- Address

- GPS Coordinates

Supports

Interactive Kenya Map.

---

## Section 3

Project Classification

Fields

- Category
- Services
- Property Type
- Tags

---

## Section 4

Client Information

Fields

- Client Name
- Visibility
- Testimonial
- Client Logo (optional)

---

## Section 5

Project Description

Fields

Short Description

Rich Description

Challenges

Solutions

Construction Process

Outcome

Lessons Learned

---

## Section 6

Timeline

Milestones

Planning

Approvals

Foundation

Structure

Roofing

Finishes

Completion

Each milestone may contain:

Images

Documents

Dates

Notes

---

## Section 7

Media

Gallery

Videos

Documents

Drone Footage

Blueprints

Before/After Images

360 Tours (Future)

---

## Section 8

SEO

Meta Title

Meta Description

Keywords

Open Graph Image

Canonical URL

Structured Data Preview

---

## Section 9

Publishing

Status

Published Date

Featured

Homepage Visibility

Portfolio Visibility

---

# Table Columns

Display

Featured Image

Project Name

County

Category

Status

Completion Year

Services

Featured

Published

Updated

Actions

---

# Filters

Status

Category

County

Service

Featured

Completion Year

Published

Created Date

Updated Date

---

# Row Actions

View

Preview

Edit

Duplicate

Publish

Archive

Restore

Export

Delete

---

# Bulk Actions

Publish

Archive

Restore

Delete

Assign Category

Assign Service

Assign Tags

Export

---

# Infolist

Display

Project Summary

↓

Gallery

↓

Timeline

↓

Services

↓

Location

↓

Documents

↓

Videos

↓

SEO

↓

Activity

↓

Metadata

---

# Relation Managers

Gallery

Timeline

Documents

Videos

Testimonials

Related Projects

Project Team (Future)

---

# Dashboard Widgets

Recently Published Projects

Draft Projects

Featured Projects

Projects by County

Projects by Category

Project Views

Recent Uploads

---

# Search Integration

Search

Project Name

County

Property Type

Services

Tags

Reference Number

UUID

Search should prioritize title matches.

---

# Redis Cache Strategy

Cache

Featured Projects

Homepage Portfolio

Recent Projects

Popular Projects

Project Statistics

County Counts

Category Counts

Invalidate cache when:

Project Published

Project Updated

Project Archived

Project Deleted

Media Changed

---

# Laravel Reverb Events

Broadcast

Project Published

Project Featured

Media Uploaded

Timeline Updated

Project Archived

Dashboard widgets should refresh automatically.

---

# Queued Jobs

Image Optimization

Video Processing

Thumbnail Generation

SEO Sitemap Update

Search Index Update

Statistics Update

Notification Dispatch

Heavy work should never occur during the request lifecycle.

---

# Notifications

Notify administrators when:

Project Published

Project Archived

Media Upload Failed

Video Processing Complete

Notify marketing users when:

Featured Project Changes

---

# Activity Logging

Log

Created

Updated

Published

Archived

Media Uploaded

Timeline Updated

SEO Updated

Exported

Viewed

---

# Reports

Projects by County

Projects by Category

Projects by Service

Completion Timeline

Featured Projects

Media Usage

SEO Performance

---

# API Exposure

Future API endpoints

Projects

Featured Projects

Categories

Locations

Search

Project Details

API should never expose unpublished projects.

---

# Testing Requirements

Feature Tests

Form Validation

Filters

Permissions

Publishing Workflow

Media Uploads

Timeline CRUD

Relation Managers

Bulk Actions

Search

Caching

Queue Dispatch

Broadcast Events

---

# AI Development Rules

AI agents should:

- Prefer reusable form sections.
- Extract repeatable schemas.
- Use UUID route binding.
- Queue all heavy operations.
- Broadcast business events.
- Cache homepage queries.
- Optimize eager loading.
- Avoid N+1 queries.
- Follow Project lifecycle.
- Never duplicate media logic.
- Never embed business logic in the Resource.

---

# Future Improvements

Project Cost Analysis

Construction Progress Tracking

Interactive Gantt Charts

BIM Integration

Drone Flight Logs

Equipment Usage

Material Consumption

Client Collaboration

Contract Documents

Project Approvals

Digital Signatures

Public Progress Pages

AI Project Summaries

Project Recommendations

Predictive Analytics


---

# Quotation Resource

> The primary sales and customer acquisition resource.

---

# Business Purpose

The Quotation Resource manages the complete lifecycle of every quotation, from the moment a visitor requests a quotation on the public website until the quotation is either accepted, rejected, expired, or converted into an active construction project.

It acts as the bridge between marketing and operations.

Every quotation represents a potential business opportunity.

---

# Business Goals

The Quotation Resource should enable the company to:

- Capture qualified enquiries
- Standardize quotation preparation
- Improve response times
- Track quotation performance
- Increase conversion rates
- Maintain communication history
- Generate professional quotations
- Convert successful quotations into projects

---

# Navigation

Sales

↓

Quotations

Icon

Heroicons DocumentText

Navigation Badge

Pending Quotations

Example

```
Pending (14)
```

Badge updates automatically.

---

# Quotation Lifecycle

```
Draft

↓

Submitted

↓

Under Review

↓

Awaiting Information

↓

Prepared

↓

Internal Approval

↓

Sent

↓

Viewed

↓

Negotiation

↓

Accepted

↓

Rejected

↓

Expired

↓

Converted To Project
```

The lifecycle should be visible to administrators and clients.

---

# Form Architecture

## Section 1

Reference Information

Fields

- Quote Number (Auto-generated)
- UUID
- Status
- Priority
- Assigned Staff
- Source

Sources

- Website
- Walk-in
- Referral
- Email
- Phone
- Existing Client

---

## Section 2

Client Information

Fields

- Client
- Company
- Contact Person
- Email
- Phone
- Preferred Contact Method

---

## Section 3

Project Information

Fields

- Requested Service
- Property Type
- County
- Town
- Site Address
- Estimated Budget
- Expected Timeline

---

## Section 4

Requirements

Fields

- Client Requirements
- Additional Notes
- Existing Drawings
- Supporting Documents
- Images
- Site Photos

---

## Section 5

Quotation Details

Fields

- Scope of Work
- Assumptions
- Deliverables
- Exclusions
- Estimated Duration
- Valid Until

---

## Section 6

Pricing

Future Ready

- Labour
- Materials
- Equipment
- Transport
- Professional Fees
- Taxes
- Discounts
- Total

Initially these may be stored as summarized values, with room for future itemization.

---

## Section 7

Attachments

- PDF Quote
- BOQ
- Drawings
- Contracts
- Supporting Documents

---

## Section 8

Internal Notes

Only visible to staff.

Used for:

- Follow-up reminders
- Negotiation notes
- Risk assessment
- Approval comments

---

## Section 9

Publishing

Fields

Status

Send Email

Notify Client

Archive

---

# Table Columns

Display

Quote Number

Client

Requested Service

County

Assigned Staff

Status

Priority

Budget

Valid Until

Updated

Actions

---

# Filters

Status

Priority

Assigned Staff

County

Requested Service

Budget Range

Created Date

Expiry Date

Converted

---

# Row Actions

View

Edit

Duplicate

Generate PDF

Email Client

Download

Mark Sent

Accept

Reject

Archive

Delete

---

# Bulk Actions

Assign Staff

Send Emails

Archive

Export

Generate PDFs

Delete (Restricted)

---

# Infolist

Summary

↓

Client

↓

Project Request

↓

Pricing

↓

Communication Timeline

↓

Attachments

↓

Activity

↓

Metadata

---

# Relation Managers

Documents

Communications

Attachments

Approvals (Future)

Tasks (Future)

Invoices (Future)

Payments (Future)

Converted Project

---

# Dashboard Widgets

Pending Quotations

Quotes Awaiting Approval

Expiring Quotes

Monthly Conversion Rate

Revenue Pipeline

Recent Requests

Average Response Time

Accepted This Month

Rejected This Month

---

# Quote Numbering Strategy

Format

```
QT-2026-000001
```

Should be sequential, human-readable, and unique.

UUID remains the primary identifier internally.

---

# Approval Workflow

Version 1

Single approval.

Future

Multi-level approvals.

Manager Approval

↓

Director Approval

↓

Finance Approval

Approval workflow should be configurable.

---

# PDF Generation

Generate professional branded quotations.

Include:

Company Branding

Client Information

Project Details

Scope

Pricing

Terms

Validity

Signature Block

QR Code (Future)

---

# Email Integration

Support

Quotation Sent

Reminder

Expiry Reminder

Accepted

Rejected

Follow-up

Emails should use branded templates.

---

# Client Portal Integration

Clients should be able to:

View quotation

Download PDF

Accept quotation

Reject quotation

Request clarification

View communication history

Upload requested documents

---

# Redis Cache Strategy

Cache

Dashboard Metrics

Revenue Pipeline

Pending Counts

Conversion Statistics

Staff Performance

Invalidate cache on:

Status changes

Assignment

Acceptance

Conversion

Deletion

---

# Laravel Reverb Events

Broadcast

Quote Submitted

Quote Assigned

Quote Sent

Quote Viewed

Quote Accepted

Quote Rejected

Quote Expired

Dashboard widgets should update in real time.

---

# Queued Jobs

Generate PDF

Send Email

Notify Client

Generate Statistics

Update CRM

Sync Future ERP

Heavy operations should always be queued.

---

# Notifications

Notify Staff

New quotation

Assignment

Acceptance

Rejection

Expiry

Notify Client

Quotation ready

Reminder

Viewed

Accepted confirmation

---

# Activity Logging

Record

Created

Updated

Assigned

Sent

Viewed

Downloaded

Accepted

Rejected

Converted

Archived

Deleted

Every action should be attributable to a user.

---

# Reports

Quotes by Month

Quotes by Service

Quotes by County

Quotes by Staff

Average Response Time

Average Quote Value

Acceptance Rate

Revenue Pipeline

Lost Opportunities

---

# API Exposure

Future endpoints

Submit Quote Request

Retrieve Quote

Accept Quote

Reject Quote

Download PDF

Quote History

Endpoints should respect authentication and authorization.

---

# Security Considerations

Clients should only access their own quotations.

PDF downloads should use signed URLs where appropriate.

Sensitive pricing information must not be exposed to unauthorized users.

Every quotation access should be auditable.

---

# Testing Requirements

Feature Tests

Lifecycle transitions

Permissions

Form validation

PDF generation

Email delivery

Queue dispatch

Broadcast events

Search

Filters

Bulk actions

Portal access

Activity logs

---

# AI Development Rules

AI agents should:

- Generate sequential quote numbers.
- Always use UUIDs internally.
- Queue PDF generation.
- Queue email delivery.
- Broadcast status updates.
- Record all activity.
- Cache dashboard metrics.
- Keep pricing logic outside the Resource.
- Follow the quotation lifecycle strictly.
- Never expose another client's quotation.

---

# Future Improvements

Digital Signatures

Electronic Acceptance

Online Payments

BOQ Builder

Cost Estimation Engine

Revision History

Versioned Quotations

Approval Pipelines

AI Cost Recommendations

AI Scope Suggestions

WhatsApp Notifications

SMS Notifications

Procurement Integration

Invoice Generation

Automatic Project Creation

ERP Synchronization

---

# Service Resource

> The authoritative catalogue of Zytech Contractors' professional services.

---

# Business Purpose

The Service Resource defines every professional service offered by Zytech Contractors.

It serves as the central source of truth for all services presented across the platform.

Services are consumed by multiple domains, including Projects, Quotations, the Knowledge Centre, SEO, Search, and future ERP modules.

A service is not merely a page on the website—it represents a business capability.

---

# Business Goals

The Service Resource should enable the company to:

- Present services professionally
- Educate potential clients
- Improve SEO
- Support quotation requests
- Categorize projects
- Build trust
- Increase conversions

---

# Navigation

Business

↓

Services

Icon

Heroicons WrenchScrewdriver

Navigation Badge

Display unpublished services.

Example

```
Draft (2)
```

---

# Service Lifecycle

```
Draft

↓

Internal Review

↓

Published

↓

Featured

↓

Archived

↓

Restored

↓

Deleted (Rare)
```

Deletion should rarely be required.

---

# Form Architecture

## Section 1

Service Information

Fields

- Service Name
- Slug
- UUID
- Status
- Featured
- Display Order

---

## Section 2

Overview

Fields

- Short Description
- Full Description
- Hero Headline
- Hero Subtitle

---

## Section 3

Service Details

Fields

- Benefits
- Deliverables
- Scope
- Limitations
- Estimated Duration
- Target Audience

---

## Section 4

Process

The company workflow.

Example

Consultation

↓

Site Visit

↓

Planning

↓

Quotation

↓

Execution

↓

Handover

Each step includes:

- Title
- Description
- Icon
- Optional Image

---

## Section 5

Frequently Asked Questions

Support multiple FAQs.

Each FAQ contains:

Question

Answer

Sort Order

Visibility

---

## Section 6

Media

Hero Image

Gallery

Illustrations

Videos

Supporting Documents

---

## Section 7

Related Content

Related Projects

Related Articles

Related Services

Cross-selling Opportunities

---

## Section 8

SEO

Meta Title

Meta Description

Keywords

Canonical URL

Open Graph

Structured Data Preview

---

## Section 9

Publishing

Status

Featured

Homepage Visibility

Service Listing Visibility

---

# Table Columns

Display

Service Image

Service Name

Category

Projects Linked

Articles Linked

Status

Featured

Updated

Actions

---

# Filters

Status

Category

Featured

Published

Updated Date

Created Date

Projects Linked

---

# Row Actions

View

Preview

Edit

Duplicate

Publish

Archive

Restore

Delete

---

# Bulk Actions

Publish

Archive

Restore

Delete

Assign Category

Export

---

# Infolist

Overview

↓

Benefits

↓

Process

↓

Projects

↓

Knowledge Articles

↓

Media

↓

SEO

↓

Metadata

---

# Relation Managers

FAQs

Projects

Articles

Media

Documents

Related Services

---

# Dashboard Widgets

Published Services

Draft Services

Most Viewed Services

Most Requested Services

Services Without Projects

Services Missing SEO

---

# Search Integration

Search by:

Service Name

Keywords

Description

Related Projects

FAQs

Categories

---

# Redis Cache Strategy

Cache

Homepage Services

Featured Services

Navigation

Popular Services

Service Statistics

Invalidate cache when:

Published

Updated

Archived

Deleted

Media Updated

SEO Updated

---

# Laravel Reverb Events

Broadcast

Service Published

Service Updated

Service Featured

FAQ Updated

Dashboard widgets should refresh automatically.

---

# Queued Jobs

Generate SEO

Refresh Sitemap

Optimize Images

Search Index Updates

Analytics Updates

Heavy tasks should execute asynchronously.

---

# Notifications

Notify administrators when:

Service Published

Service Archived

SEO Issues Detected

Media Processing Failed

---

# Activity Logging

Record

Created

Updated

Published

Archived

Media Uploaded

SEO Updated

FAQ Updated

Viewed

Exported

---

# Reports

Services by Category

Most Requested Services

Most Viewed Services

Quote Requests by Service

Projects by Service

Services Missing Content

SEO Completion

---

# API Exposure

Future endpoints

List Services

Service Details

Featured Services

Search Services

Related Projects

Related Articles

Public APIs should expose only published services.

---

# Security Considerations

Only authorized staff may:

Create

Publish

Archive

Delete

Featured status should require elevated permissions.

---

# Testing Requirements

Feature Tests

Validation

Publishing Workflow

FAQ CRUD

Search

Filters

Media Upload

SEO Fields

Permissions

Cache Invalidation

Broadcast Events

Queue Dispatch

---

# AI Development Rules

AI agents should:

- Use reusable form sections.
- Reuse FAQ components.
- Extract Process Builder components.
- Queue expensive tasks.
- Cache homepage queries.
- Keep SEO synchronized.
- Never duplicate business logic.
- Always update related search indexes.
- Follow the Service lifecycle.

---

# Future Improvements

Service Packages

Pricing Templates

Interactive Cost Calculator

AI Service Recommendations

Lead Scoring

Conversion Analytics

Industry-specific Variations

Multi-language Support

Video Walkthroughs

Service Comparison Tool

Service Availability by Region

Customer Success Stories

---

# Client Resource

> The central customer relationship management resource for the Zytech Contractors Platform.

---

# Business Purpose

The Client Resource manages every individual and organization that interacts with Zytech Contractors.

It serves as the authoritative customer record across the entire platform.

Every quotation, project, communication, invoice, document, notification, and portal account should ultimately relate back to a Client.

The Client Resource forms the foundation of the future CRM.

---

# Business Goals

The Client Resource should enable the company to:

- Maintain a complete customer profile
- Track every interaction
- Manage quotations
- Manage portal access
- Store project history
- Improve customer service
- Support long-term relationships
- Prepare for ERP integration

---

# Navigation

Sales

↓

Clients

Icon

Heroicons UserGroup

Navigation Badge

New Clients

Portal Invitations Pending

Inactive Clients

---

# Client Lifecycle

```
Lead

↓

Prospect

↓

Quotation Requested

↓

Quotation Sent

↓

Negotiation

↓

Customer

↓

Returning Customer

↓

VIP Customer

↓

Inactive

↓

Archived
```

Clients should never normally be deleted.

---

# Client Classification

Support classifications.

Residential

Commercial

Corporate

Government

NGO

Developer

Property Management

Educational Institution

Healthcare

Industrial

Multiple classifications should be allowed.

---

# Form Architecture

## Section 1

Identity

Fields

- UUID
- Client Type
- Company Name
- Contact Person
- Registration Number (optional)

---

## Section 2

Contact Information

Fields

- Email
- Phone Number
- Alternative Phone
- WhatsApp Number
- Preferred Communication Method

---

## Section 3

Location

Fields

Country

County

Town

Postal Address

Physical Address

Google Maps Location

---

## Section 4

Portal Access

Fields

Portal Enabled

Email Verified

Invitation Sent

Last Login

Portal Status

Preferred Language

Two Factor Enabled (Future)

---

## Section 5

Business Information

Industry

Company Size

Website

Tax Number

Preferred Payment Terms

Account Manager

---

## Section 6

Relationship

Lead Source

Referral

Assigned Staff

Customer Rating

Priority

Tags

---

## Section 7

Internal Notes

Private Notes

Risk Assessment

Preferred Communication Times

Customer Preferences

These fields are visible only to authorized staff.

---

## Section 8

Attachments

Identification

Contracts

Certificates

Supporting Documents

Signed Agreements

---

## Section 9

Status

Lifecycle Stage

Archived

Blocked

Portal Enabled

---

# Table Columns

Display

Avatar

Client Name

Company

Email

Phone

Lifecycle

Portal

Projects

Quotations

Last Activity

Updated

Actions

---

# Filters

Lifecycle

Client Type

County

Portal Enabled

Assigned Staff

Lead Source

Rating

Created Date

Updated Date

Last Login

---

# Row Actions

View

Edit

Invite to Portal

Reset Portal Access

Send Email

Call

Export Profile

Archive

Delete (Restricted)

---

# Bulk Actions

Invite to Portal

Assign Account Manager

Export

Archive

Tag Clients

Send Emails

---

# Infolist

Profile

↓

Contact Information

↓

Projects

↓

Quotations

↓

Communications

↓

Portal Activity

↓

Documents

↓

Activity Log

↓

Metadata

---

# Relation Managers

Projects

Quotations

Communications

Documents

Invoices (Future)

Payments (Future)

Tasks (Future)

Meetings (Future)

Support Tickets (Future)

Notifications

---

# Client Timeline

Every interaction should appear in chronological order.

Example

Account Created

↓

Quotation Requested

↓

Quotation Sent

↓

Client Viewed Quote

↓

Accepted

↓

Project Started

↓

Project Completed

↓

Requested New Quote

This becomes the complete relationship history.

---

# Dashboard Widgets

New Clients

Active Clients

Returning Clients

Portal Invitations Pending

Top Clients

Recent Sign-ins

Client Growth

Lead Conversion

---

# Portal Integration

Portal Status

Invitation Date

Last Login

Active Sessions (Future)

Unread Notifications

Pending Quotations

Open Documents

Portal access is managed entirely from this resource.

---

# Communication Integration

Future integrations

Email

WhatsApp

SMS

Phone Logs

Meeting Notes

Video Calls

Every communication should be attached to the client.

---

# Search Integration

Search by

Client Name

Company

Phone

Email

UUID

Project

Quotation Number

Reference Number

Search should be optimized for customer support workflows.

---

# Redis Cache Strategy

Cache

Client Statistics

Dashboard Metrics

Recent Clients

Portal Counts

Lead Counts

Invalidate cache when

Client Updated

Portal Activated

Quotation Accepted

Project Created

Client Archived

---

# Laravel Reverb Events

Broadcast

Client Registered

Portal Activated

Quotation Accepted

New Message

Portal Login

Profile Updated

Dashboard widgets should update automatically.

---

# Queued Jobs

Portal Invitation Email

Reminder Emails

Statistics Updates

Notification Delivery

CRM Synchronization (Future)

Large exports

Heavy tasks should always execute asynchronously.

---

# Notifications

Notify Staff

New Client

Portal Registration

Quote Accepted

VIP Client Activity

Notify Client

Portal Invitation

Password Setup

Quotation Ready

Project Updates

New Documents

Messages

---

# Activity Logging

Log

Client Created

Profile Updated

Portal Enabled

Invitation Sent

Logged In

Quotation Accepted

Document Uploaded

Archived

Exported

Every action should include:

User

Timestamp

IP Address (where appropriate)

Affected Entity

---

# Reports

Client Growth

Client Retention

Lead Sources

Top Clients

Projects per Client

Average Revenue per Client (Future)

Portal Usage

Client Activity

Response Time

---

# API Exposure

Future endpoints

Register Client

Retrieve Profile

Portal Authentication

Documents

Notifications

Projects

Quotations

The API should never expose another client's data.

---

# Security Considerations

Every client must only access:

Their own profile

Their own quotations

Their own documents

Their own communications

Their own projects

Use Laravel Policies and UUID route model binding for all protected resources.

Sensitive information should be encrypted where appropriate.

---

# Testing Requirements

Feature Tests

Portal Invitations

Authentication

Authorization

Lifecycle Changes

Search

Filters

Communication Logging

Document Uploads

Notifications

Queue Dispatch

Broadcast Events

Cache Invalidation

---

# AI Development Rules

AI agents should:

- Never expose another client's information.
- Always use UUID route model binding.
- Queue all email delivery.
- Broadcast portal events.
- Cache dashboard metrics.
- Log every significant interaction.
- Keep CRM logic outside the Resource.
- Respect lifecycle transitions.
- Use Policies for every action.
- Avoid duplicated communication logic.

---

# Future Improvements

Customer Satisfaction Surveys

AI Lead Scoring

AI Customer Insights

Customer Segmentation

WhatsApp Integration

SMS Integration

Meeting Scheduler

Support Ticketing

Client Notes AI Summaries

Referral Tracking

Loyalty Programme

Customer Health Score

Automatic Follow-up Reminders

Client Mobile App

ERP Customer Synchronization

---

# Company Resource

> The authoritative source of truth for Zytech Contractors' corporate identity, branding, contact information, and public company profile.

---

# Business Purpose

The Company Resource manages every piece of information that represents Zytech Contractors publicly and internally.

Instead of hardcoding company information throughout the application, all business identity should originate from this resource.

This ensures consistency across the website, quotations, PDFs, email templates, APIs, and future systems.

---

# Business Goals

The Company Resource should enable administrators to:

- Maintain corporate identity
- Manage branding assets
- Manage office locations
- Manage company history
- Showcase leadership
- Display certifications
- Present awards
- Maintain statistics
- Control homepage content
- Maintain contact information
- Manage legal information

---

# Navigation

Company

↓

Company Profile

Icon

Heroicons BuildingOffice2

Since there will only be one company profile, this resource should function as a singleton.

Users should not be able to create multiple companies.

---

# Singleton Pattern

The Company Resource represents Zytech Contractors.

There is exactly one Company Profile.

Administrators edit the existing profile.

Creation should only happen during initial platform installation.

---

# Company Sections

The resource is divided into logical sections.

```
Overview

↓

Brand Identity

↓

Company Story

↓

Leadership

↓

Statistics

↓

Offices

↓

Certifications

↓

Awards

↓

Partners

↓

Contact

↓

Social Media

↓

Legal

↓

SEO

↓

Homepage Content
```

---

# Form Architecture

## Section 1

Company Identity

Fields

- Company Name
- Registered Name
- Tagline
- Short Description
- Company Motto
- UUID

---

## Section 2

Brand Assets

Fields

- Primary Logo (SVG)
- Secondary Logo
- White Logo
- Dark Logo
- Favicon
- Apple Touch Icon
- Brand Guidelines PDF

Image uploads should automatically generate optimized versions using Spatie Media Library.

---

## Section 3

Company Story

Fields

- About Us
- Mission
- Vision
- Core Values
- History
- Why Choose Us

Rich text should support reusable content blocks.

---

## Section 4

Leadership

Fields

- Managing Director
- Executive Team
- Directors
- Senior Staff

Each profile includes:

- Name
- Position
- Biography
- Photo
- LinkedIn
- Email (optional)

Future support:

Team relation manager.

---

## Section 5

Company Statistics

Statistics displayed on the website.

Examples

Projects Completed

Years of Experience

Satisfied Clients

Professional Staff

Counties Served

Square Metres Built

These statistics should support animated counters.

---

## Section 6

Office Locations

Support multiple offices.

Fields

Office Name

County

Town

Physical Address

Postal Address

Google Maps Coordinates

Opening Hours

Phone Numbers

Emails

Primary Office

Future:

Interactive office locator.

---

## Section 7

Certifications

Support multiple certifications.

Examples

NCA Registration

Engineers Board

Architect Registration

ISO Certification

Occupational Safety

Upload

Certificate

Issue Date

Expiry Date

Verification URL

---

## Section 8

Awards

Award Name

Organization

Year

Description

Photo

Certificate

---

## Section 9

Partners

Partner Name

Logo

Website

Description

Relationship Type

Future

Partner Portal

---

## Section 10

Contact Information

Primary Phone

Secondary Phone

WhatsApp

Email

Sales Email

Support Email

Emergency Contact

Support multiple contact methods.

---

## Section 11

Social Media

Facebook

Instagram

LinkedIn

TikTok

YouTube

Pinterest

X (Twitter)

Future

Threads

---

## Section 12

Legal

Fields

Company Registration

VAT Number

PIN Number

Terms

Privacy Policy

Cookies Policy

Business Licenses

---

## Section 13

Homepage

Homepage Hero

Video Background

Call To Action

Homepage Statistics

Featured Projects

Featured Services

Testimonials

Partner Logos

Everything on the homepage should be configurable from here.

---

## Section 14

SEO

Meta Title

Meta Description

Knowledge Graph

Organization Schema

Local Business Schema

OpenGraph

Twitter Cards

Canonical

Robots

Structured Data Preview

---

# Infolist

Display

Company Overview

↓

Brand Assets

↓

Leadership

↓

Statistics

↓

Locations

↓

Certifications

↓

Awards

↓

Partners

↓

SEO

↓

Metadata

---

# Relation Managers

Office Locations

Leadership

Awards

Certifications

Partners

Testimonials

Homepage Slides

Documents

Media

---

# Dashboard Widgets

Company Completeness

SEO Score

Expired Certifications

Upcoming Renewals

Homepage Health

Broken Social Links

---

# Search Integration

Search

Company Name

Leadership

Office

Certification

Award

Partner

---

# Redis Cache Strategy

Cache

Homepage

Navigation

Footer

Company Statistics

Contact Information

Partner Logos

Homepage Hero

Testimonials

Featured Content

Invalidate cache when:

Company Profile Updated

Logo Updated

Homepage Updated

Certification Updated

Office Updated

---

# Laravel Reverb Events

Broadcast

Homepage Updated

Statistics Changed

New Certification

Logo Updated

Office Updated

Widgets should refresh automatically.

---

# Queued Jobs

Optimize Images

Generate Favicons

Regenerate SEO

Rebuild Sitemap

Generate Social Images

Thumbnail Generation

Heavy operations should never execute synchronously.

---

# Notifications

Notify administrators when:

Certification Expiring

Broken Social Link

Missing SEO

Homepage Media Missing

Logo Upload Failed

---

# Activity Logging

Record

Company Updated

Logo Changed

Office Added

Certification Added

Homepage Modified

SEO Updated

Leadership Updated

---

# Reports

Certification Status

Homepage Completion

SEO Health

Office Distribution

Leadership Directory

Partner Summary

---

# API Exposure

Future endpoints

Company Profile

Leadership

Offices

Statistics

Partners

Contact Information

Homepage

Only public information should be exposed.

---

# Security Considerations

Restrict editing to authorized administrators.

Legal information should require elevated permissions.

Sensitive business information should never be exposed publicly.

Media uploads should be virus-scanned and validated.

---

# Testing Requirements

Feature Tests

Singleton behavior

Validation

Media uploads

Office CRUD

Certification CRUD

SEO updates

Cache invalidation

Broadcast events

Queue dispatch

Authorization

---

# AI Development Rules

AI agents should:

- Treat Company Resource as a singleton.
- Never create duplicate company profiles.
- Reuse media components.
- Queue image processing.
- Cache homepage content.
- Broadcast homepage updates.
- Keep branding consistent.
- Never hardcode company information elsewhere.

---

# Future Improvements

Multi-company support

Multi-brand support

Regional offices

Interactive office map

Organization chart

Investor relations

CSR initiatives

Annual reports

Sustainability dashboard

Company timeline visualization

Brand asset versioning

AI-generated company summaries

# Knowledge Article Resource

> The authoritative knowledge and content marketing engine for the Zytech Contractors Platform.

---

# Business Purpose

The Knowledge Article Resource manages educational, technical, and marketing content published by Zytech Contractors.

Unlike a traditional blog, the Knowledge Centre is designed to establish industry authority, educate prospective clients, improve search engine visibility, and generate qualified leads.

Every article should solve a real problem faced by property owners, developers, architects, or construction professionals.

---

# Business Goals

The Knowledge Centre should:

- Build trust
- Improve SEO
- Generate leads
- Support Services
- Support Projects
- Educate clients
- Increase website traffic
- Demonstrate expertise
- Improve conversions
- Become Kenya's leading construction resource

---

# Navigation

Content

↓

Knowledge Centre

Icon

Heroicons AcademicCap

Navigation Badge

Draft Articles

Scheduled Articles

Needs Review

---

# Article Lifecycle

```
Draft

↓

In Review

↓

SEO Review

↓

Scheduled

↓

Published

↓

Updated

↓

Archived

↓

Deleted (Rare)
```

Publishing should support scheduled releases.

---

# Article Types

Support multiple content types.

Educational Guide

Construction Tips

Project Showcase

Case Study

Industry News

Building Regulations

Cost Guide

Architecture

Interior Design

Property Investment

FAQs

Announcements

Thought Leadership

---

# Content Pillars

Content should belong to one or more pillars.

Residential Construction

Commercial Construction

Architecture

Interior Design

Property Development

Planning & Approvals

Construction Costs

Project Management

Renovations

Maintenance

Sustainability

Smart Buildings

These pillars support SEO clustering.

---

# Form Architecture

## Section 1

Article Information

Fields

- Title
- Slug
- UUID
- Article Type
- Content Pillar
- Status
- Featured

---

## Section 2

Author

Fields

Author

Editor

Reviewer

Publication Date

Estimated Reading Time

Expert Reviewed

---

## Section 3

Content

Fields

Excerpt

Introduction

Body

Summary

Call To Action

Support:

Markdown

Rich Editor

Reusable Content Blocks

Code Blocks (rare)

Tables

Construction Diagrams

---

## Section 4

Featured Media

Fields

Featured Image

Gallery

Videos

PDF Downloads

Infographics

Blueprints

---

## Section 5

Related Content

Related Services

Related Projects

Related Articles

Downloads

FAQs

This should create automatic internal linking.

---

## Section 6

SEO

Meta Title

Meta Description

Keywords

Canonical URL

Focus Keyword

OpenGraph

Twitter Cards

Schema Preview

Robots

---

## Section 7

AI SEO

Fields

AI Summary

FAQ Schema

How-To Schema

Article Schema

Construction Entity Tags

Voice Search Questions

Semantic Keywords

Related Questions

Future:

LLM Metadata

---

## Section 8

Publishing

Status

Scheduled Date

Featured

Homepage Visibility

Newsletter

Notify Subscribers

---

# Table Columns

Featured Image

Title

Author

Article Type

Pillar

Status

Featured

Views

Reading Time

Published

Updated

Actions

---

# Filters

Status

Author

Editor

Pillar

Article Type

Featured

Published

Scheduled

Created Date

Updated Date

Reading Time

---

# Row Actions

View

Preview

Edit

Duplicate

Publish

Schedule

Archive

Generate SEO

Generate AI Summary

Delete

---

# Bulk Actions

Publish

Archive

Schedule

Assign Author

Assign Category

Generate SEO

Export

Delete

---

# Infolist

Article

↓

SEO

↓

AI SEO

↓

Related Services

↓

Related Projects

↓

Media

↓

Analytics

↓

Activity

↓

Metadata

---

# Relation Managers

Media

Downloads

FAQs

Related Articles

Related Services

Related Projects

Comments (Future)

Newsletter Campaigns

---

# Dashboard Widgets

Draft Articles

Scheduled Articles

Most Read Articles

Top Performing Content

Articles Missing SEO

Articles Missing Featured Images

Average Reading Time

Organic Traffic

---

# Search Integration

Search

Title

Slug

Content

Keywords

Pillars

Author

Services

Projects

Full-text PostgreSQL search should be implemented.

---

# Redis Cache Strategy

Cache

Homepage Articles

Featured Articles

Popular Articles

Recent Articles

Related Articles

Category Pages

Sidebar Content

Invalidate cache when:

Published

Updated

Archived

Deleted

SEO Updated

Media Updated

---

# Laravel Reverb Events

Broadcast

Article Published

Article Scheduled

SEO Updated

Featured Changed

Widgets should refresh instantly.

---

# Queued Jobs

Generate Sitemap

Refresh Search Index

Optimize Images

Generate AI Summary

Generate Social Images

Notify Subscribers

Refresh Related Content

Update Statistics

---

# Notifications

Notify editors when:

Review Requested

SEO Missing

Media Missing

Article Published

Schedule Failed

Notify subscribers

New Article

Featured Guide

Newsletter

---

# Activity Logging

Log

Created

Updated

Published

Scheduled

Archived

Viewed

Downloaded

SEO Generated

AI Summary Generated

Newsletter Sent

---

# Reports

Articles by Pillar

Most Read

Top CTR

Most Shared

SEO Completion

Average Reading Time

Traffic Sources

Conversion Rate

Lead Attribution

---

# SEO Strategy

Every article should target:

One primary keyword

Three secondary keywords

Related entities

Frequently Asked Questions

Construction terminology

Internal links

External authority references

Schema markup

Each article should satisfy Google's EEAT principles.

---

# AI SEO Strategy

Every article should include:

AI Summary

Entity Extraction

Topic Relationships

FAQ Schema

How-To Schema (where applicable)

Construction glossary

Semantic keyword coverage

Voice search optimization

Future support:

LLM metadata

Knowledge Graph optimization

---

# API Exposure

Future endpoints

Articles

Featured Articles

Search

Categories

Related Content

Popular Articles

RSS Feed

Only published content should be exposed.

---

# Security Considerations

Draft articles should never be publicly accessible.

Publishing requires elevated permissions.

Scheduled publishing should be queue-driven.

Downloads should support signed URLs where appropriate.

---

# Testing Requirements

Feature Tests

Publishing

Scheduling

SEO Generation

Media Upload

Related Content

Search

Permissions

Cache

Queues

Broadcasting

API

---

# AI Development Rules

AI agents should:

- Reuse editor components.
- Generate slugs automatically.
- Queue expensive operations.
- Cache homepage content.
- Maintain internal linking.
- Never duplicate SEO metadata.
- Follow EEAT principles.
- Follow content pillar strategy.
- Generate structured data.
- Keep article lifecycle consistent.

---

# Future Improvements

AI Writing Assistant

Content Approval Workflow

Version History

Multi-language Support

Newsletter Automation

Podcast Generation

Video Transcripts

Interactive Calculators

Construction Glossary

Question & Answer Hub

Client Success Stories

Construction Cost Database

Interactive Building Guides

AI Content Recommendations

Topic Gap Analysis

Content Performance Predictions

Semantic Search

Personalized Reading Experience

---

# Media Library Resource

> Enterprise Digital Asset Management (DAM) powered by Spatie Media Library.

---

# Business Purpose

The Media Library Resource manages every digital asset used throughout the Zytech Contractors Platform.

Rather than allowing every module to upload files independently, all assets should pass through a centralized media management system.

This provides:

- Consistency
- Versioning
- Optimization
- Organization
- Security
- Performance
- Auditability

The Media Library becomes the single source of truth for all digital assets.

---

# Business Goals

The Media Library should enable administrators to:

- Upload media
- Organize assets
- Search assets
- Tag assets
- Version assets
- Optimize images
- Manage videos
- Manage documents
- Track usage
- Reuse assets

---

# Navigation

Content

↓

Media Library

Icon

Heroicons Photo

Navigation Badge

Processing Files

Failed Uploads

Unused Assets

---

# Supported Asset Types

Images

Videos

Documents

PDFs

SVG Logos

Blueprints

CAD Exports (Future)

Drone Footage

360 Images

Audio (Future)

ZIP Files

---

# Asset Lifecycle

```
Upload

↓

Virus Scan

↓

Validation

↓

Optimization

↓

Thumbnail Generation

↓

Responsive Conversion

↓

Storage

↓

Usage

↓

Archive

↓

Delete
```

Heavy processing should never occur during the request lifecycle.

---

# Storage Strategy

Storage should support:

Public Assets

Private Assets

Temporary Assets

Client Documents

Generated PDFs

Future Cloud Storage

Each collection should have its own storage rules.

---

# Media Collections

Projects

Services

Articles

Homepage

Company

Clients

Quotations

Documents

Users

System

Email Templates

Exports

Backups (metadata only)

---

# Form Architecture

## Section 1

Upload

Fields

- File Upload
- Drag & Drop
- Multi Upload
- Folder Selection

---

## Section 2

Metadata

Fields

Title

Alt Text

Caption

Description

Copyright

Photographer

Source

License

Visibility

---

## Section 3

Classification

Category

Tags

Department

Project

Service

Knowledge Article

Client

Quotation

---

## Section 4

Optimization

Generate WebP

Generate AVIF

Generate Responsive Images

Crop

Resize

Compress

Watermark (optional)

---

## Section 5

SEO

Image Alt

Image Title

OpenGraph

Social Preview

Structured Data

---

# Table Columns

Preview

Filename

Collection

Type

Size

Resolution

Usage Count

Uploaded By

Created

Actions

---

# Filters

Collection

Type

Uploaded By

Unused

Referenced

Large Files

Recently Uploaded

Processing Status

---

# Row Actions

Preview

Download

Replace

Rename

Move

Duplicate

Optimize

Generate Conversions

Archive

Delete

---

# Bulk Actions

Optimize

Move

Assign Tags

Archive

Delete

Generate WebP

Generate AVIF

Regenerate Thumbnails

---

# Infolist

Preview

↓

Metadata

↓

Usage

↓

Relationships

↓

Versions

↓

Conversions

↓

Downloads

↓

Activity

↓

Storage Details

---

# Relation Managers

Projects

Services

Articles

Company

Clients

Quotations

Users

Documents

---

# Image Conversions

Every uploaded image should automatically generate:

Original

↓

Thumbnail

↓

Small

↓

Medium

↓

Large

↓

WebP

↓

AVIF

↓

Social Image

↓

Hero Banner

↓

Gallery Version

All conversions should be queued.

---

# Responsive Images

Every image should support responsive rendering.

Support:

Mobile

Tablet

Desktop

Retina

Large Desktop

Never serve oversized images.

---

# Video Processing

Support

MP4

WebM

MOV

Future

HLS Streaming

Generate

Poster Image

Thumbnail

Preview Clip

Duration

Resolution

Codec

---

# Document Management

Support

PDF

DOCX

XLSX

PPTX

ZIP

Generate previews where possible.

Support version history.

---

# Search Integration

Search

Filename

Title

Alt Text

Caption

Tags

Project

Service

Article

Uploader

---

# Redis Cache Strategy

Cache

Homepage Images

Featured Images

Company Logos

Hero Banners

Frequently Used Assets

Invalidate cache when:

Asset Updated

Asset Deleted

Asset Replaced

Conversion Completed

---

# Laravel Reverb Events

Broadcast

Upload Complete

Optimization Complete

Conversion Complete

Processing Failed

Replacement Complete

Widgets update automatically.

---

# Queued Jobs

Virus Scan

Optimize Images

Generate Conversions

Generate WebP

Generate AVIF

Thumbnail Generation

Video Processing

Metadata Extraction

AI Image Analysis (Future)

---

# Notifications

Notify users when:

Upload Finished

Optimization Failed

Storage Full

Virus Detected

Large Upload Completed

---

# Activity Logging

Record

Uploaded

Downloaded

Viewed

Optimized

Replaced

Deleted

Archived

Converted

Moved

Tagged

---

# Reports

Storage Usage

Largest Files

Unused Assets

Most Downloaded

Media by Collection

Media Growth

Optimization Savings

Storage Savings

---

# API Exposure

Future endpoints

Media Search

Upload

Download

Conversions

Metadata

Collections

Signed URLs should be used for protected assets.

---

# Security Considerations

Validate MIME types.

Virus scan uploads.

Restrict executable files.

Use signed URLs for private assets.

Limit upload size.

Prevent duplicate uploads using file hashes.

Encrypt sensitive documents at rest where appropriate.

---

# Performance Strategy

All image processing should be queued.

Serve responsive images.

Prefer AVIF.

Fallback to WebP.

Fallback to original.

Lazy load images.

Use CDN-ready URLs.

Support HTTP caching.

---

# Testing Requirements

Feature Tests

Upload

Validation

Optimization

Conversions

Downloads

Permissions

Cache

Queues

Broadcasting

Search

---

# AI Development Rules

AI agents should:

- Never bypass Spatie Media Library.
- Queue all media processing.
- Generate responsive images.
- Generate AVIF and WebP.
- Keep metadata synchronized.
- Avoid duplicate uploads.
- Use media collections consistently.
- Cache frequently accessed assets.
- Never store media outside configured disks.
- Always provide alt text support.

---

# Future Improvements

AI Auto Tagging

OCR

Face Detection

Object Recognition

Duplicate Detection

Background Removal

Automatic Watermarking

Video Transcoding

CDN Integration

Cloudflare Images

S3 Storage

Azure Blob Storage

Google Cloud Storage

Version Comparison

AI Generated Captions

Semantic Search

Visual Similarity Search

Asset Approval Workflow

# User Resource

> Enterprise Identity & Access Management (IAM) powered by Laravel, Filament and Spatie Permission.

---

# Business Purpose

The User Resource manages every authenticated individual who interacts with the Zytech Contractors Platform.

It provides centralized identity management, authentication, authorization, auditing, and access control across all administrative systems.

Every action performed within the platform should ultimately be attributable to an authenticated user.

The User Resource forms the security foundation of the platform.

---

# Business Goals

The User Resource should enable administrators to:

- Manage user accounts
- Assign roles
- Assign permissions
- Manage departments
- Control access
- Audit activity
- Monitor sessions
- Secure authentication
- Support future SSO
- Support future multi-tenancy

---

# Navigation

Administration

↓

Users

Icon

Heroicons Users

Navigation Badge

Inactive Users

Pending Invitations

Locked Accounts

---

# User Lifecycle

```
Invitation Sent

↓

Account Created

↓

Email Verified

↓

Active

↓

Suspended

↓

Locked

↓

Archived

↓

Deleted (Rare)
```

Deletion should almost never occur.

Archiving is preferred.

---

# Authentication Strategy

Authentication will use Laravel Authentication.

Support

Email Login

Password Login

Remember Me

Password Reset

Email Verification

Session Management

Future

Single Sign-On

OAuth

Azure AD

Google Workspace

Microsoft 365

---

# UUID Strategy

Every user will use UUIDs.

Used for

Routes

Activity Logs

Notifications

Broadcast Events

API Responses

Relationships

Never expose numeric IDs.

---

# User Types

System Administrator

Managing Director

Operations Manager

Project Manager

Architect

Engineer

Quantity Surveyor

Estimator

Sales Representative

Marketing Officer

Finance Officer

HR Officer

Content Editor

Support Staff

Client

Future

Vendor

Supplier

Subcontractor

Auditor

---

# Departments

Administration

Projects

Architecture

Engineering

Sales

Marketing

Finance

Procurement

Human Resources

Operations

Customer Support

Departments are organizational only.

Permissions control access.

---

# Form Architecture

## Section 1

Identity

Fields

First Name

Last Name

Display Name

UUID

Employee Number

Job Title

Department

Avatar

---

## Section 2

Authentication

Email

Username (future)

Password

Password Confirmation

Email Verified

Two Factor Enabled

Account Status

---

## Section 3

Roles

Primary Role

Additional Roles

Permissions

Permission Overrides (rare)

---

## Section 4

Contact Information

Phone

Alternative Phone

Emergency Contact

Office Extension

Location

---

## Section 5

Employment

Department

Manager

Employment Date

Employment Status

Employee Type

Future

Organization Hierarchy

---

## Section 6

Preferences

Language

Timezone

Theme

Dashboard Layout

Notification Preferences

Default Landing Page

---

## Section 7

Security

Failed Login Attempts

Last Login

Last Password Change

Force Password Reset

Require MFA

Trusted Devices

---

## Section 8

Portal Access

Administrative Access

Client Portal Access

API Access

Mobile Access (future)

---

# Table Columns

Avatar

Name

Department

Primary Role

Status

Last Login

Email Verified

MFA

Created

Actions

---

# Filters

Department

Role

Status

MFA Enabled

Email Verified

Last Login

Created Date

Archived

---

# Row Actions

View

Edit

Reset Password

Send Invitation

Lock

Unlock

Suspend

Activate

Archive

Delete (Restricted)

Impersonate (Super Admin)

---

# Bulk Actions

Activate

Suspend

Archive

Assign Role

Assign Department

Force Password Reset

Export

---

# Infolist

Profile

↓

Roles

↓

Permissions

↓

Departments

↓

Activity Timeline

↓

Sessions

↓

Devices

↓

Notifications

↓

Metadata

---

# Relation Managers

Roles

Permissions

Sessions

Activity Logs

Notifications

API Tokens (future)

Devices (future)

Projects

Quotations

Approvals

---

# Dashboard Widgets

Online Users

Recently Logged In

Failed Login Attempts

Locked Accounts

Users by Department

Users by Role

Pending Invitations

Inactive Accounts

---

# Role Strategy

Use Spatie Permission.

A user may have multiple roles.

Permissions should be assigned through roles.

Direct permissions should be exceptional.

Never duplicate permissions.

---

# Permission Categories

Projects

Services

Clients

Quotations

Knowledge Centre

Media

Company

Users

Reports

Settings

System

Every permission should follow a consistent naming convention.

Example

projects.view

projects.create

projects.update

projects.delete

projects.publish

projects.archive

---

# Policy Strategy

Every business resource must have a corresponding Laravel Policy.

Examples

ProjectPolicy

ClientPolicy

QuotePolicy

ServicePolicy

ArticlePolicy

MediaPolicy

CompanyPolicy

SettingsPolicy

Never perform authorization checks directly inside controllers or Livewire components.

---

# Session Management

Track

Current Session

Last Login

Browser

Operating System

IP Address

Approximate Location

Future

Concurrent Session Management

Remote Logout

Device Trust

---

# Account Lockout

Automatically lock accounts after repeated failed login attempts.

Administrators should be able to:

Unlock

Reset Password

Force Logout

---

# Password Policy

Minimum 12 characters.

Require:

Uppercase

Lowercase

Number

Special Character

Prevent reuse of recent passwords.

Future

HIBP password breach checks.

---

# Multi-Factor Authentication

Version 1

Email Verification

Optional TOTP

Future

Authenticator Apps

Security Keys (WebAuthn)

Passkeys

SMS (if required)

Backup Codes

---

# Impersonation

Super Administrators may impersonate users.

Requirements

Display visible banner.

Audit every impersonation.

Log start and end time.

Prevent nested impersonation.

Never allow impersonation of another Super Administrator.

---

# Notifications

Notify users when

Password Changed

New Login

Role Changed

Permission Changed

Account Locked

MFA Enabled

Invitation Sent

---

# Redis Cache Strategy

Cache

Role Lists

Permission Matrix

Navigation

Dashboard Widgets

Department Statistics

Invalidate cache on

Role Updated

Permission Updated

User Updated

Department Changed

---

# Laravel Reverb Events

Broadcast

User Logged In

User Logged Out

Invitation Accepted

Role Changed

Account Locked

Widgets should update in real time.

---

# Queued Jobs

Invitation Emails

Password Reset Emails

Security Alerts

Large Exports

Permission Synchronization

Audit Reports

---

# Activity Logging

Record

Created

Updated

Logged In

Logged Out

Password Changed

Role Changed

Permission Changed

Impersonated

Locked

Unlocked

Archived

Every event should include:

Actor

Timestamp

IP Address

Device

User Agent

---

# Reports

Users by Department

Role Distribution

Permission Matrix

Login Frequency

Security Events

Inactive Accounts

Failed Logins

Audit Summary

---

# API Exposure

Future endpoints

Current User

Profile

Sessions

Notifications

Preferences

API Tokens

Role Information

Only authenticated users should access these endpoints.

---

# Security Considerations

Enforce Policies for every action.

Encrypt sensitive fields.

Protect against brute-force attacks.

Expire inactive sessions.

Rotate session IDs after login.

Log all privileged actions.

Restrict impersonation to Super Administrators.

---

# Testing Requirements

Feature Tests

Authentication

Authorization

Role Assignment

Permission Checks

Session Management

Password Reset

Account Lockout

Impersonation

MFA

Activity Logs

Notifications

Cache

Broadcasting

Queue Dispatch

---

# AI Development Rules

AI agents should:

- Use Spatie Permission exclusively for RBAC.
- Never hardcode role names.
- Always authorize using Policies.
- Queue emails and security alerts.
- Broadcast authentication events.
- Log all privileged actions.
- Keep authentication logic separate from Resources.
- Never bypass UUID route model binding.
- Prefer role-based permissions over direct permissions.
- Follow the account lifecycle consistently.

---

# Future Improvements

Single Sign-On (SSO)

OAuth Providers

Passkeys (WebAuthn)

SCIM User Provisioning

Organization Hierarchies

Temporary Elevated Access

Delegated Administration

Just-In-Time Permissions

Risk-Based Authentication

Behavioral Login Detection

Security Dashboard

Identity Federation

Device Trust Management

Adaptive Authentication

Multi-Tenant Identity Support

# Settings Resource

> The centralized control center for configuring, managing, and monitoring the Zytech Contractors Platform.

---

# Business Purpose

The Settings Resource provides a centralized interface for managing all configurable aspects of the application.

Rather than scattering configuration throughout the application, administrators should manage website behavior, branding, integrations, communication, performance, security, and operational settings from a unified interface.

The Settings Resource follows the Singleton Resource pattern.

Only one configuration profile exists.

---

# Business Goals

Administrators should be able to:

- Configure the website
- Configure company branding
- Configure SEO
- Configure email
- Configure notifications
- Configure Redis
- Configure Queues
- Configure Reverb
- Configure Storage
- Configure Security
- Configure APIs
- Configure Analytics
- Configure Maintenance
- Configure Feature Flags

without modifying application code.

---

# Navigation

Administration

↓

System Settings

Icon

Heroicons Cog6Tooth

Singleton Resource

---

# Settings Modules

```
System Settings

│

├── General

├── Branding

├── Website

├── Homepage

├── SEO

├── Email

├── Notifications

├── Redis

├── Queue

├── Broadcasting

├── Storage

├── Security

├── Integrations

├── Feature Flags

├── Performance

├── Maintenance

├── Monitoring

├── Backup

└── System Information
```

---

# Module 1

General Settings

Fields

Application Name

Application URL

Timezone

Locale

Date Format

Currency

Measurement Units

Default Country

Default County

Google Maps Default Coordinates

UUID Namespace

---

# Module 2

Brand Settings

Fields

Primary Logo

Secondary Logo

Dark Logo

Light Logo

Favicon

Brand Colors

Typography

Default Icons

PDF Branding

Email Branding

Invoice Branding

---

# Module 3

Website Settings

Fields

Homepage Enabled

Maintenance Banner

Announcement Bar

Footer Text

Contact CTA

WhatsApp CTA

Quote CTA

Live Chat Enabled

Newsletter Enabled

Search Enabled

---

# Module 4

Homepage Settings

Configure

Hero

Featured Services

Featured Projects

Testimonials

Counters

Partners

Awards

Knowledge Articles

Statistics

Homepage Layout

---

# Module 5

SEO Settings

Default Meta Title

Default Description

Robots.txt

Sitemap

Canonical Domain

Organization Schema

Business Schema

OpenGraph Defaults

Twitter Defaults

Indexing Rules

AI Metadata

Knowledge Graph

---

# Module 6

Email Settings

SMTP

Mailgun

SES

Postmark

Reply-To

BCC

Queue Emails

Email Signature

Email Footer

Email Templates

---

# Module 7

Notification Settings

Enable

Email

SMS

WhatsApp (Future)

Push Notifications

Slack

Discord

Telegram

Daily Reports

Weekly Reports

Admin Alerts

---

# Module 8

Redis Configuration

Display

Current Driver

Connected

Memory Usage

Hit Rate

Cached Keys

Configure

Default TTL

Cache Prefix

Session Cache

Query Cache

Widget Cache

Page Cache

Invalidate Rules

---

# Module 9

Queue Configuration

Drivers

Redis

Database

Future

SQS

Configure

Queue Names

Retries

Timeouts

Failed Jobs

Priority Queues

Supervisor Status

---

# Module 10

Broadcasting

Driver

Laravel Reverb

Fields

Server Status

Port

Host

Secure Connection

Broadcast Prefix

Reconnect Strategy

Heartbeat

Future

Pusher

Ably

---

# Module 11

Storage

Configured Disks

Local

Public

Private

Future

S3

Cloudflare R2

Azure Blob

Google Cloud Storage

Display

Storage Usage

Largest Files

Available Space

---

# Module 12

Security

Password Policy

MFA Policy

Session Lifetime

Login Attempts

Rate Limits

API Limits

Allowed Origins

CSP

Security Headers

Signed URLs

Audit Settings

---

# Module 13

Integrations

Google Maps

Google Analytics

Google Search Console

Google Tag Manager

Meta Pixel

LinkedIn Pixel

reCAPTCHA

Cloudflare

OpenAI (Future)

Maps API

SMTP Providers

---

# Module 14

Feature Flags

Client Portal

Knowledge Centre

Interactive Map

AI SEO

Live Chat

Online Payments

Project Timeline

Before/After Slider

Newsletter

API

Dark Mode

Experimental Features

---

# Module 15

Performance

Cache Status

Redis Status

Queue Status

Broadcast Status

Database Status

Media Optimization

Image Compression

Lazy Loading

Preloading

Compression

Asset Versioning

---

# Module 16

Maintenance

Enable Maintenance

Whitelist IPs

Maintenance Message

Estimated Completion

Maintenance Banner

Scheduled Maintenance

Auto Maintenance

---

# Module 17

Monitoring

Display

CPU

RAM

Disk

Queue Workers

Redis

PostgreSQL

Apache

Laravel Reverb

Storage

Application Version

PHP Version

Laravel Version

Livewire Version

Filament Version

---

# Module 18

Backup

Backup Frequency

Retention

Storage

Encryption

Verification

Database Backup

Media Backup

Configuration Backup

Download Backup

Restore (Restricted)

---

# Dashboard Widgets

Application Health

Redis Status

Queue Workers

Broadcast Status

Disk Usage

Failed Jobs

Recent Deployments

Cache Hit Rate

Security Alerts

Backup Status

---

# Redis Cache Strategy

Cache

Settings

Branding

Navigation

Homepage

SEO Defaults

Integrations

Feature Flags

Invalidate

Only affected modules

Avoid full cache flushes.

---

# Laravel Reverb Events

Broadcast

Maintenance Enabled

Settings Updated

Brand Updated

Feature Flag Changed

Homepage Updated

System Health Changed

---

# Queued Jobs

Backup

Image Optimization

Email Verification

Cache Warming

Generate Sitemap

Analytics Reports

Health Checks

Queue Cleanup

---

# Activity Logging

Log

Configuration Changes

Brand Updates

Security Updates

Feature Flag Changes

Maintenance Mode

Backup Execution

Every configuration change should be attributable to an authenticated administrator.

---

# Reports

Configuration Changes

Security Events

Backup History

System Health

Performance Metrics

Queue Statistics

Redis Statistics

Storage Growth

---

# API Exposure

Future

System Health

Configuration

Feature Flags

Application Version

Public Settings

Only expose non-sensitive information.

---

# Security Considerations

Encrypt sensitive configuration values.

Never expose secrets.

Mask API Keys.

Mask SMTP Passwords.

Restrict access using dedicated Policies.

Require elevated permissions for security settings.

Log every configuration change.

---

# Testing Requirements

Feature Tests

Singleton Behavior

Validation

Configuration Persistence

Redis Integration

Queue Configuration

Reverb Configuration

Feature Flags

Maintenance Mode

Backups

Authorization

Cache Invalidation

Broadcast Events

---

# AI Development Rules

AI agents should:

- Treat Settings as a singleton.
- Never duplicate configuration elsewhere.
- Read configuration from dedicated services.
- Cache frequently accessed settings.
- Never hardcode URLs, emails, or branding.
- Queue expensive operations.
- Broadcast configuration changes.
- Log every settings update.
- Use strongly typed configuration DTOs.
- Validate all configuration before persistence.

---

# Future Improvements

Configuration Versioning

Configuration Rollback

Environment Comparison

Blue/Green Deployment Settings

Secrets Manager Integration

Feature Rollouts

A/B Testing

Health Dashboard

Deployment Dashboard

Real-time Metrics

Cluster Support

Multi-region Configuration

Tenant-specific Settings (Future)

Configuration Import/Export

# System Resource

> Enterprise Operations Center for monitoring, maintaining, and operating the Zytech Contractors Platform.

---

# Business Purpose

The System Resource provides administrators with a centralized operational dashboard for monitoring application health, infrastructure status, queues, Redis, broadcasting, scheduled tasks, storage, logs, backups, and deployments.

Unlike traditional CRUD resources, this module is operational rather than business-focused.

Its purpose is to ensure the platform remains healthy, secure, performant, and available.

---

# Business Goals

Administrators should be able to:

- Monitor application health
- Monitor infrastructure
- View queue workers
- Manage failed jobs
- Monitor Redis
- Monitor PostgreSQL
- Monitor Reverb
- Monitor Apache
- Monitor scheduled tasks
- Monitor storage
- Manage cache
- View logs
- Manage backups
- Review deployments
- Receive alerts

---

# Navigation

Administration

↓

Operations Center

Icon

Heroicons ServerStack

Navigation Badge

Critical Alerts

Failed Jobs

Offline Services

---

# Operations Modules

```

Operations Center

│

├── Application Health

├── Infrastructure

├── Queue Workers

├── Redis

├── PostgreSQL

├── Broadcasting

├── Scheduler

├── Storage

├── Logs

├── Cache

├── Backups

├── Deployments

├── Security

├── Monitoring

└── Diagnostics

```

---

# Module 1

Application Health

Display

Laravel Version

PHP Version

Filament Version

Livewire Version

Composer Version

Environment

Debug Mode

Maintenance Mode

Application Uptime

Current Release

Git Commit Hash

Branch

---

# Module 2

Infrastructure

Display

Apache Status

PHP-FPM Status

Redis Status

PostgreSQL Status

Laravel Reverb Status

Queue Workers

Disk Space

Memory

CPU

Load Average

---

# Module 3

Queue Workers

Display

Running Workers

Pending Jobs

Completed Jobs

Failed Jobs

Retry Count

Longest Running Job

Queue Throughput

Queue Latency

Future

Supervisor Integration

Laravel Horizon Support

Horizon is the **primary** queue operations UI.

Administrators should use `/horizon` for:

- Queue dashboards
- Failed jobs
- Retries
- Throughput monitoring
- Worker balancing

The Filament Operations Center may deep-link to Horizon rather than duplicating queue tooling.

---

# Module 4

Redis

Display

Connection

Memory Usage

Cache Size

Hit Rate

Miss Rate

Key Count

Expired Keys

Evictions

Connections

Slow Commands

---

# Module 5

PostgreSQL

Display

Connection

Active Sessions

Slow Queries

Database Size

Locks

Long Transactions

Replication Status (Future)

Extensions

Index Health

Vacuum Status

---

# Module 6

Broadcasting

Driver

Laravel Reverb

Display

Connected Clients

Channels

Events Broadcast

Failed Events

Reconnect Count

Average Latency

---

# Module 7

Scheduler

Display

Scheduled Commands

Next Execution

Previous Execution

Failed Commands

Execution Time

Skipped Tasks

Manual Run

---

# Module 8

Storage

Display

Public Storage

Private Storage

Media Storage

Free Space

Largest Files

Storage Growth

Orphaned Files

Duplicate Files

---

# Module 9

Logs

Display

Application Logs

Security Logs

Queue Logs

Broadcast Logs

HTTP Errors

PHP Errors

Warning Count

Critical Count

Search

Download

Archive

---

# Module 10

Cache

Display

Redis Cache

Application Cache

Route Cache

Config Cache

View Cache

Compiled Services

Actions

Warm Cache

Clear Cache

Selective Cache Flush

Never perform global cache clearing unless explicitly confirmed.

---

# Module 11

Backups

Display

Database Backup

Media Backup

Configuration Backup

Last Backup

Next Backup

Backup Size

Verification Status

Retention

Download

Restore (Restricted)

---

# Module 12

Deployments

Display

Current Version

Previous Version

Deployment Time

Commit Hash

Branch

Deployment User

Deployment Notes

Future

Rollback

Deployment History

CI/CD Integration

---

# Module 13

Security

Display

Failed Logins

Locked Accounts

Password Resets

MFA Adoption

Suspicious Activity

Blocked Requests

Security Headers

Rate Limit Events

Audit Summary

---

# Module 14

Monitoring

Display

Average Response Time

95th Percentile

Error Rate

Queue Throughput

Cache Hit Rate

Broadcast Latency

Database Latency

Memory Usage

CPU Usage

Pulse Dashboard Link (`/pulse`)

Horizon Dashboard Link (`/horizon`)

---

# Module 15

Diagnostics

Run

Health Check

Database Check

Redis Check

Storage Check

Queue Check (Horizon)

Broadcast Check

Mail Check

Configuration Check

Generate Diagnostic Report

---

# Dashboard Widgets

Application Health

Infrastructure Status

Redis Health

Queue Status (Horizon)

Failed Jobs

Storage Usage

Database Health

Broadcast Health

System Alerts

Recent Deployments

Backup Status

Pulse Summary

---

# Redis Cache Strategy

Cache

Health Statistics

Queue Metrics

Dashboard Widgets

System Information

Invalidate

Only affected metrics.

Never clear operational cache unnecessarily.

---

# Laravel Reverb Events

Broadcast

Worker Offline

Queue Failed

Health Changed

Backup Complete

Deployment Completed

Redis Disconnected

Database Slow Query

Widgets should update in real time.

---

# Queued Jobs

Health Checks

Cleanup Jobs

Backup Verification

Storage Analysis

Log Rotation

Statistics Collection

Diagnostic Reports

---

# Notifications

Notify administrators when:

Redis Offline

Database Offline

Queue Failure

Broadcast Failure

Backup Failure

Disk Full

Memory High

Slow Queries

Security Incident

Critical Error

Support multiple notification channels:

Email

Slack

Microsoft Teams (Future)

Telegram

SMS (Future)

---

# Activity Logging

Record

Cache Cleared

Deployment

Backup

Restore

Diagnostics

Manual Health Check

Maintenance Mode

Queue Restart

System Restart

Configuration Change

Every operational action must be fully auditable.

---

# Reports

Infrastructure Health

Queue Performance

Redis Performance

Database Performance

Application Errors

Security Events

Storage Growth

Deployment History

Backup Success Rate

System Availability

---

# API Exposure

Future endpoints

System Health

Metrics

Queue Status

Broadcast Status

Storage

Monitoring

Diagnostic Report

Protected by administrator-only authentication.

---

# Security Considerations

Operations Center is accessible only to privileged administrators.

Critical actions require confirmation.

Potentially destructive operations should require re-authentication.

Diagnostic reports must exclude sensitive secrets.

API keys and credentials should never be displayed.

---

# Testing Requirements

Feature Tests

Health Checks

Queue Monitoring

Redis Monitoring

Storage Monitoring

Broadcast Monitoring

Log Viewer

Backups

Diagnostics

Authorization

Notifications

Broadcast Events

Cache

---

# AI Development Rules

AI agents should:

- Never execute destructive operations automatically.
- Prefer read-only diagnostics.
- Queue long-running system tasks via Horizon.
- Prefer Horizon / Pulse over reinventing operational dashboards.
- Cache monitoring metrics efficiently.
- Broadcast operational state changes.
- Log every administrative action.
- Avoid exposing sensitive configuration.
- Validate service availability before reporting status.
- Keep monitoring components modular and reusable.

---

# Future Improvements

~~Laravel Pulse Integration~~ → **Adopted** (`/pulse`)

~~Laravel Horizon Integration~~ → **Adopted** (`/horizon`)

~~Laravel Telescope Integration~~ → **Adopted** (local debugging)

OpenTelemetry

Prometheus Exporter

Grafana Dashboards

Sentry Integration

Uptime Monitoring

Status Page

Distributed Tracing

Container Monitoring

Kubernetes Support

Blue/Green Deployment Monitoring

Multi-Server Clusters

Predictive Failure Detection

AI Anomaly Detection

Automatic Incident Reports

Self-Healing Workflows

# Filament Design Principles

## 1. UUID First

- Never expose numeric IDs.
- Use UUID route model binding.
- UUIDs are the primary public identifier.

---

## 2. Policies Everywhere

Every Resource must have a Laravel Policy.

Never authorize inside controllers or Livewire components.

---

## 3. Queue Heavy Operations

Never process expensive work during an HTTP request.

Use queues for:

- Image processing
- PDF generation
- Email delivery
- Notifications
- Search indexing
- Statistics
- AI processing

---

## 4. Broadcast Business Events

Every meaningful business event should broadcast through Laravel Reverb.

Examples:

- Quote accepted
- Project published
- Client registered
- Article published

---

## 5. Redis First

Cache expensive queries.

Never cache mutable transactional data.

Use tagged cache where possible.

---

## 6. Thin Resources

Resources orchestrate.

Services contain business logic.

Actions perform work.

Repositories retrieve data.

Policies authorize.

Events notify.

---

## 7. Activity Logging

Every important action must be auditable.

Never mutate important business data silently.

---

## 8. Modular Forms

Extract reusable:

- Form schemas
- Table schemas
- Filters
- Infolists
- Widgets

---

## 9. Performance First

- Eager loading
- Cursor pagination where appropriate
- Lazy loading
- Responsive images
- Queues
- Redis
- PostgreSQL indexes

---

## 10. Enterprise Ready

Every module should be designed with future support for:

- ERP
- CRM
- Mobile App
- APIs
- AI
- Multi-tenancy
- Multi-company
