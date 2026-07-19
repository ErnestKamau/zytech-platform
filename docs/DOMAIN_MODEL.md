# Zytech Contractors Platform

# 06_DOMAIN_MODEL.md

> Version: 1.0
> Status: Living Document
> Project: Zytech Contractors Digital Platform
> Last Updated: July 2026

---

# Table of Contents

1. Purpose
2. Domain Philosophy
3. Business Capability Map
4. Core Domains
5. Domain Ownership
6. Domain Relationships
7. Domain Communication
8. Domain Events
9. Domain Lifecycle
10. Future Domains
11. Domain Engineering Principles

---

# Purpose

This document defines the business domains of the Zytech Contractors Platform.

Rather than organizing the platform around technical concepts such as controllers or database tables, the system is organized around business capabilities.

Each domain owns a clearly defined part of the business.

Every feature belongs to exactly one domain.

Every business object has one owner.

Every responsibility has one home.

---

# Domain Philosophy

The platform is designed around the language of the business.

Developers should think in terms of:

• Projects

• Clients

• Quotations

• Company

• Services

• Knowledge

—not—

• Models

• Controllers

• Components

• Requests

Business capabilities drive software architecture.

---

# Business Capability Map

```text
                    Zytech Contractors Platform

                                │
     ┌──────────────────────────┼──────────────────────────┐
     │                          │                          │
 Company                  Client Experience         Operations
     │                          │                          │
     │                          │                          │
 Services                 Quotations                 Projects
     │                          │                          │
     └──────────────┬───────────┴───────────────┐
                    │                           │
              Knowledge Centre             Communication
                    │                           │
                    └──────────────┬────────────┘
                                   │
                             Notifications
```

Every capability contributes to the overall customer journey.

---

# Core Domains

The platform consists of the following business domains.

| Domain | Priority | Purpose |
|----------|----------|--------------------------------|
| Company | Core | Company identity and branding |
| Services | Core | Construction services |
| Projects | Core | Portfolio and completed work |
| Quotations | Core | Sales and quotation workflow |
| Clients | Core | Customer relationships |
| Knowledge | Strategic | Educational content |
| Media | Infrastructure | Images, videos and documents |
| Notifications | Infrastructure | Customer communication |
| Users | Infrastructure | Internal users and permissions |
| Settings | Infrastructure | Business configuration |

---

# Domain Classification

## Revenue Domains

Directly contribute to generating revenue.

- Services
- Projects
- Quotations

---

## Customer Experience Domains

Improve customer engagement.

- Company
- Knowledge Centre
- Client Portal
- Notifications

---

## Platform Domains

Support business operations.

- Users
- Settings
- Media
- Search
- Analytics

---

## Future ERP Domains

Planned expansion.

- Procurement
- Inventory
- Accounting
- Finance
- HR
- Fleet
- Equipment
- Assets
- Suppliers

---

# Domain Ownership

Every business capability belongs to one domain.

Shared ownership is prohibited.

| Business Object | Owner |
|----------------|----------------|
| Company Profile | Company |
| Team Members | Company |
| Services | Services |
| Projects | Projects |
| Project Timeline | Projects |
| Project Gallery | Projects |
| Project Videos | Projects |
| Quote Request | Quotations |
| Quotation | Quotations |
| Client | Clients |
| Knowledge Article | Knowledge |
| Testimonial | Projects |
| Company Statistics | Company |
| Documents | Media |

Ownership defines responsibility.

Only the owning domain may modify its business rules.

---

# Domain Lifecycle

Every domain follows a common lifecycle.

```text
Created

↓

Validated

↓

Published

↓

Maintained

↓

Archived

↓

Restored (Optional)

↓

Deleted (Rare)
```

Business entities should be archived rather than deleted whenever possible.

---

# Guiding Principles

Each domain should satisfy the following principles.

✓ Single Responsibility

✓ High Cohesion

✓ Low Coupling

✓ Independent Evolution

✓ Event Driven

✓ Easily Testable

✓ Replaceable

✓ Discoverable

No domain should become a dependency bottleneck.


---
# Company Domain

Purpose

Represents the public identity of Zytech Contractors.

Owns

- Company Profile
- Mission
- Vision
- Core Values
- Team
- Certifications
- Social Links
- Business Information

Consumes

- Media

Produces

- CompanyUpdated
- TeamMemberAdded
- TeamMemberUpdated
- CertificationAdded
- CompanyStatisticsUpdated

Business Goal

Increase customer confidence before first contact.

---

# Services Domain

Purpose

Defines every professional service offered by the company.

Owns

- Services
- Service Categories
- FAQs
- Service Benefits
- Service Process

Consumes

- Projects
- Knowledge Articles

Produces

- ServiceCreated
- ServiceUpdated
- ServicePublished
- ServiceArchived

Business Goal

Explain company expertise while generating qualified enquiries.

---
# Projects Domain

Purpose

Acts as the company's digital portfolio.

Owns

- Projects
- Categories
- Timeline
- Gallery
- Videos
- Before & After
- Locations
- Tags
- Related Projects

Consumes

- Services
- Media

Produces

- ProjectCreated
- ProjectUpdated
- ProjectPublished
- ProjectFeatured
- ProjectArchived
- ProjectViewed

Business Goal

Demonstrate capability through real completed work.

---

# Quotations Domain

Purpose

Manages the complete quotation lifecycle.

Owns

- Quote Requests
- Quotations
- Quote Documents
- Quote Messages

Consumes

- Clients
- Services
- Projects

Produces

- QuoteRequested
- QuoteReviewed
- QuotePrepared
- QuoteSent
- QuoteAccepted
- QuoteRejected
- QuoteConverted

Business Goal

Convert enquiries into construction projects.

---

# Clients Domain

Purpose

Represents customers throughout their relationship with the company.

Owns

- Client Profile
- Contact Details
- Portal Access
- Preferences

Consumes

- Quotations
- Projects

Produces

- ClientRegistered
- ClientVerified
- ClientUpdated
- ClientInvited

Business Goal

Provide an excellent customer experience before, during, and after projects.

---

# Knowledge Domain

Purpose

Educate visitors while strengthening search visibility.

Owns

- Articles
- Categories
- Tags
- Guides

Consumes

- Services
- Projects

Produces

- ArticlePublished
- ArticleUpdated
- ArticleArchived

Business Goal

Increase trust while generating organic traffic.

---

# Media Domain

Purpose

Manage all digital assets used throughout the platform.

Owns

- Images
- Videos
- Documents
- Media Collections
- Media Conversions

Consumes

Every business domain.

Produces

- MediaUploaded
- MediaOptimized
- MediaDeleted

Business Goal

Deliver high-quality media efficiently.

---

Company
    │
    ├──────────────┐
    │              │
Services       Knowledge
    │              │
    └──────┬───────┘
           │
       Projects
           │
           │
      Quotations
           │
        Clients
           │
   Notifications

   ---

## Preferred

Domain

↓

Event

↓

Listener

↓

Queue

↓

Notification

---

## Avoid

Domain

↓

Domain

↓

Domain

↓

Domain

↓

Model

Long dependency chains increase coupling.

Whenever practical, domains should communicate through business events rather than direct service calls.

---

1.

Every business object has exactly one owner.

---

2.

Every business rule belongs to exactly one domain.

---

3.

Domains communicate through events whenever possible.

---

4.

A domain should be removable with minimal impact on unrelated domains.

---

5.

A developer should always ask:

"What business capability am I implementing?"

before writing code.

---

6.

Framework code must never define business architecture.

Business architecture defines framework implementation.

---

7.

New domains should be introduced only when they represent genuine business capabilities—not technical convenience.