# Zytech Contractors Platform

# 05_FEATURES.md

> Version: 1.0
> Status: Living Document
> Project: Zytech Contractors Digital Platform
> Last Updated: July 2026

---

# Table of Contents

1. Product Vision
2. Product Objectives
3. Product Principles
4. Target Users
5. User Roles
6. Platform Overview
7. Website Modules
8. Administration Portal
9. Client Portal
10. Communication Features
11. Search Features
12. Media Management
13. Knowledge Centre
14. Notifications
15. Analytics
16. Authentication
17. Authorization
18. Future ERP Modules
19. Mobile Readiness
20. API Readiness
21. Feature Development Lifecycle
22. Definition of Done
23. Feature Readiness Matrix

---

# Product Vision

The Zytech Contractors Platform is designed to become the company's complete digital ecosystem.

The first release focuses on building a premium, conversion-focused marketing website that showcases the company's expertise, generates qualified leads, and simplifies customer engagement.

Future releases will progressively expand into a fully integrated construction management platform supporting clients, internal staff, business operations, analytics, and enterprise resource planning (ERP).

The platform should reflect the same qualities customers expect from the company itself:

- Professional
- Reliable
- Modern
- Precise
- Transparent
- Innovative

Every feature should contribute to improving customer trust while reducing internal administrative effort.

---

# Feature Classification Framework

To ensure engineering effort is proportional to business value, every feature within the Zytech Contractors Platform is assigned a Feature Level.

Feature Levels influence:

- Engineering effort
- Documentation depth
- UI/UX polish
- Testing requirements
- Performance optimization
- Security requirements
- Code review standards
- AI implementation expectations

Higher-level features require significantly greater attention to architecture, scalability, testing, and user experience.

---

## Level 1 — Core Business Features

These features define the platform.

Without them, the platform loses its primary business value.

Characteristics

- Mission critical
- Highest engineering priority
- Rich UX
- Extensive documentation
- Advanced testing
- High scalability
- Maximum performance optimization

Examples

- Projects
- Quotations
- Client Portal
- Project Management (Future)

Engineering Requirements

✓ Complete Product Specification

✓ Domain Model

✓ Database Specification

✓ Events

✓ Queues

✓ Broadcasting

✓ Redis Cache

✓ Filament Resources

✓ Livewire Components

✓ Feature Tests

✓ Browser Tests

✓ Performance Tests

✓ Accessibility Review

✓ SEO Review

---

## Level 2 — Strategic Features

Features that significantly improve conversions, customer trust, and user engagement.

Examples

- Homepage
- Company
- Services
- Knowledge Centre

Requirements

✓ Full Product Specification

✓ Responsive Design

✓ SEO Optimization

✓ Analytics

✓ Redis Cache

✓ Feature Tests

---

## Level 3 — Supporting Features

Important features that complement the platform but are not core revenue generators.

Examples

- Contact
- Team
- Testimonials
- FAQs

Requirements

✓ Documentation

✓ Responsive Design

✓ Feature Tests

---

## Level 4 — Infrastructure Features

Cross-cutting technical capabilities shared across multiple modules.

Examples

- Authentication
- Authorization
- Notifications
- Search
- Media Library
- Settings
- Activity Logs

Requirements

✓ High Test Coverage

✓ Security Review

✓ Performance Validation

✓ Integration Tests

---

## Level 5 — Future Platform Features

Planned capabilities that are architecturally supported but implemented in later releases.

Examples

- Inventory
- Procurement
- Accounting
- HR
- Fleet
- Equipment
- Finance
- Mobile Applications
- Public API

These modules should influence current architecture without increasing unnecessary complexity.

---

# Engineering Priority Matrix

| Level | Business Value | Engineering Depth | Documentation | Testing |
|---------|---------------|-------------------|---------------|----------|
| L1 | Critical | Extensive | Extensive | Extensive |
| L2 | High | Advanced | Detailed | Comprehensive |
| L3 | Medium | Moderate | Standard | Standard |
| L4 | Technical | Advanced | Detailed | Extensive |
| L5 | Future | Planned | Architectural | N/A |

---

# Definition of Excellence

Every feature should be evaluated against the following quality attributes.

## Functional

- Solves a business problem.
- Meets acceptance criteria.
- Produces measurable value.

---

## Technical

- Clean architecture.
- Modular.
- Event-driven.
- Testable.
- Maintainable.

---

## User Experience

- Responsive.
- Accessible.
- Fast.
- Intuitive.
- Delightful.

---

## Operational

- Observable.
- Logged.
- Cached where appropriate.
- Queue-aware.
- Failure tolerant.

---

## Future Ready

Every feature should be designed with future expansion in mind.

Today's implementation should not prevent tomorrow's capabilities.

---

# Feature Capsule Philosophy

Every major feature is treated as a self-contained Feature Capsule.

A Feature Capsule contains everything required to design, build, operate, test, and evolve a feature.

```text
Feature Capsule

Business
│
├── Product Goals
├── User Stories
├── Business Rules
├── Acceptance Criteria
│
Engineering
│
├── Domain
├── Database
├── Actions
├── Events
├── Jobs
├── Policies
├── Value Objects
├── Services
│
Presentation
│
├── Livewire
├── Blade
├── Alpine
├── Filament
│
Infrastructure
│
├── Redis
├── Reverb
├── Queues
├── Search
├── Media
│
Quality
│
├── Feature Tests
├── Browser Tests
├── Accessibility
├── Performance
├── SEO Validation
│
Documentation
│
├── Product Spec
├── Architecture
├── ADR
└── Changelog
```

A feature is considered complete only when its entire Feature Capsule is complete.

---

# Guiding Principle

Features are not pages.

Features are business capabilities composed of multiple architectural, technical, and operational concerns working together to deliver measurable value.

---

# Product Objectives

The platform should achieve the following business goals.

## Marketing

- Showcase completed projects.
- Demonstrate technical expertise.
- Build brand credibility.
- Differentiate Zytech from competitors.
- Improve search engine visibility.

---

## Sales

- Generate qualified leads.
- Simplify quotation requests.
- Reduce friction during customer enquiries.
- Improve conversion rates.

---

## Operations

- Centralize company information.
- Streamline quotation workflows.
- Improve customer communication.
- Reduce manual administrative work.

---

## Customer Experience

- Provide an intuitive browsing experience.
- Showcase project quality.
- Deliver fast page loading.
- Offer a modern responsive interface.

---

## Long-Term Vision

The platform should become the technological foundation for:

- Client Portal
- Project Management
- Construction ERP
- Mobile Applications
- Customer Self-Service
- Business Analytics

---

# Product Principles

Every feature developed for the platform should satisfy at least one of the following principles.

## Inspire Trust

Visitors should immediately perceive the company as experienced, reliable, and professional.

---

## Showcase Expertise

The platform should demonstrate engineering excellence through projects, services, educational content, and company achievements.

---

## Generate Business

Every page should guide visitors toward meaningful engagement.

Examples include:

- Request Quote
- Contact Company
- View Projects
- Schedule Consultation

---

## Educate Customers

Construction projects involve significant financial decisions.

The platform should educate visitors before asking them to buy.

Educational content increases trust and improves SEO.

---

## Scale Gracefully

Features should support future expansion without requiring redesign.

---

## Performance First

Fast websites convert better.

Every feature should be optimized for speed.

---

## Mobile First

Most visitors will access the website from mobile devices.

The mobile experience must be considered before desktop enhancements.

---

## Accessibility

The platform should be usable by as many people as possible.

Accessibility is considered part of product quality—not an optional enhancement.

---

# Target Users

The platform serves four primary user groups.

## Visitors

Potential customers researching construction services.

Typical goals:

- Learn about the company
- Explore services
- View projects
- Compare work quality
- Request quotations
- Contact the business

---

## Clients

Existing customers with ongoing or completed engagements.

Current goals:

- Access quotations
- View shared documents
- Review communication

Future goals:

- Monitor project progress
- Receive updates
- Review reports
- Download invoices
- Approve documents

---

## Staff

Employees responsible for delivering services.

Typical responsibilities:

- Manage quotations
- Upload projects
- Respond to enquiries
- Publish articles
- Manage customer relationships

---

## Administrators

Responsible for platform management.

Responsibilities include:

- User management
- Content management
- Analytics
- Settings
- Permissions
- Platform configuration

---

# User Roles

| Role | Description |
|----------|------------------------------------------------|
| Visitor | Anonymous public user |
| Client | Authenticated customer |
| Staff | Internal employee |
| Administrator | Full platform administrator |

Future releases may introduce additional roles including:

- Project Manager
- Architect
- Engineer
- Quantity Surveyor
- Procurement Officer
- Finance Officer
- HR
- Director

---

# Platform Overview

The platform consists of three major systems.

```text
Zytech Platform

├── Public Website
│
├── Administration Portal
│
└── Client Portal
```

Each system serves a distinct audience while sharing the same business domains and infrastructure.

---

# Public Website

Purpose:

Present the company's brand, expertise, services, and completed work while generating qualified leads.

Primary capabilities include:

- Homepage
- Company Profile
- Services
- Projects
- Project Details
- Knowledge Centre
- Testimonials
- Team
- Contact
- Quote Request
- Search

---

# Administration Portal

Built using Filament.

Primary responsibilities:

- Website Management
- Project Management
- Media Management
- Knowledge Centre
- Quote Management
- User Management
- Analytics
- Settings

---

# Client Portal

The client portal begins with quotation management and gradually evolves into a customer workspace.

Phase One includes:

- Secure Login
- Dashboard
- Quotations
- Documents
- Notifications
- Profile Management

Future phases will include:

- Project Tracking
- Timeline Updates
- Progress Reports
- Payments
- Contracts
- Communication

---

# Homepage Module

---

# Purpose

The Homepage serves as the company's digital headquarters.

Its primary objective is to establish trust, communicate professionalism, showcase capability, and guide visitors toward meaningful engagement.

Unlike a traditional brochure website, the homepage should function as a strategic sales tool that introduces Zytech Contractors, demonstrates expertise, and encourages visitors to request quotations or explore completed projects.

The homepage should immediately communicate:

- Professionalism
- Experience
- Quality
- Trust
- Innovation
- Attention to Detail

Visitors should understand what the company does within the first few seconds of arriving.

---

# Business Goals

The homepage exists to achieve measurable business outcomes.

Primary goals include:

• Increase quotation requests

• Increase project enquiries

• Showcase completed work

• Build customer confidence

• Improve search engine visibility

• Highlight company expertise

• Increase visitor engagement

• Encourage exploration of services

• Promote educational content

• Reinforce brand identity

---

# Success Metrics

Success should be measured using analytics.

Key Performance Indicators (KPIs)

- Quote Request Conversion Rate
- Contact Form Conversion
- CTA Click Rate
- Project Detail Views
- Services Viewed
- Knowledge Centre Visits
- Average Session Duration
- Scroll Depth
- Bounce Rate
- Returning Visitors

---

# Primary Call To Action

The homepage has one primary objective:

Generate qualified quotation requests.

Primary CTA

```
Request a Free Consultation
```

Secondary CTA

```
Explore Our Projects
```

Tertiary CTA

```
View Our Services
```

Every homepage section should naturally guide visitors toward one of these actions.

---

# Homepage Information Architecture

```
Hero

↓

Trust Indicators

↓

Services

↓

Featured Projects

↓

Company Statistics

↓

Why Choose Zytech

↓

Construction Process

↓

Testimonials

↓

Knowledge Centre

↓

Interactive Kenya Project Map

↓

Quote CTA

↓

Footer
```

The homepage should tell a story rather than present disconnected sections.

---

# Visitor Journey

```
Visitor Arrives

↓

Hero communicates expertise

↓

Trust established

↓

Views Projects

↓

Explores Services

↓

Reads Testimonials

↓

Views Construction Process

↓

Requests Quotation
```

Every interaction should reduce uncertainty and increase confidence.

---

# Hero Section

## Purpose

Create an immediate emotional impact while communicating professionalism.

The Hero should answer three questions instantly.

1.

Who are you?

2.

What do you build?

3.

Why should I trust you?

---

## Components

Large cinematic background

High-quality construction imagery or video

Headline

Supporting copy

Primary CTA

Secondary CTA

Trust badges

Animated statistics

---

## Headline Guidelines

Headlines should communicate confidence.

Examples

```
Building Kenya's Future With Precision

```

```
From Vision To Reality

```

```
Engineering Excellence. Built To Last.

```

Avoid generic marketing language.

---

## Hero Media

Priority

1.

Background Video

2.

Construction Drone Footage

3.

Completed Project Showcase

4.

Animated Image Carousel

Videos should be optimized and lazy-loaded.

---

# Trust Indicators

Immediately below the Hero.

Purpose:

Reduce visitor uncertainty.

Examples

✓ Years of Experience

✓ Completed Projects

✓ Counties Served

✓ Professional Team

✓ Client Satisfaction

✓ Licensed Professionals

Each statistic should animate when entering the viewport.

---

# Services Preview

Purpose

Introduce the company's capabilities.

Display:

Interior Design

Exterior Design

Property Sketching

Plan Estimation

Plan Approvals

Each service card includes:

Icon

Short Description

Learn More

Related Projects

---

# Featured Projects

Purpose

Demonstrate quality rather than merely describe it.

Display

Project Image

Category

Location

Completion Year

Short Summary

Services Used

View Project

Hover animations should communicate craftsmanship.

---

# Company Statistics

Display key achievements.

Examples

Projects Completed

Years Experience

Happy Clients

Professionals

Counties Served

Each statistic should animate only once.

---

# Why Choose Zytech

Purpose

Differentiate the company from competitors.

Suggested cards

Quality Workmanship

Transparent Communication

Experienced Professionals

On-Time Delivery

Regulatory Compliance

Client-Centered Approach

Each card should include an icon and concise explanation.

---

# Construction Process

Purpose

Educate potential clients about how projects are delivered.

Suggested steps

1.

Consultation

↓

2.

Site Visit

↓

3.

Design

↓

4.

Approvals

↓

5.

Construction

↓

6.

Completion

↓

7.

Handover

Animated timeline preferred.

---

# Testimonials Preview

Purpose

Provide social proof.

Each testimonial includes:

Customer

Project

Photo (optional)

Rating

Quote

Link to related project

---

# Knowledge Centre Preview

Purpose

Improve SEO while educating visitors.

Display:

Latest Articles

Popular Guides

Featured Construction Tips

Each article displays:

Image

Title

Category

Reading Time

Publication Date

---

# Interactive Project Map

Purpose

Demonstrate geographic reach.

Visitors can:

Select county

View projects

Open project details

Filter by service

Future

Heatmap visualization

---

# Quote CTA

The strongest conversion section.

Headline

```
Let's Build Something Exceptional Together
```

Supporting text

CTA

```
Request Your Free Consultation
```

Optional quick quotation form.

---

# Footer

Should contain

Company Information

Navigation

Services

Projects

Knowledge Centre

Contact

Social Links

Newsletter

Business Registration

Copyright

Privacy Policy

Terms

---

UX Requirements

The homepage should feel alive without becoming distracting.

Motion should communicate quality rather than decoration.

Recommended interactions:

Smooth scrolling
Reveal-on-scroll animations
Subtle parallax
Counter animations
Hover elevation
Before/after sliders
Lazy-loaded media
Skeleton loading for dynamic sections
Micro-interactions on CTAs

Animations must never compromise performance.

Accessibility Requirements
WCAG 2.2 AA target.
Semantic HTML.
Keyboard navigable.
Visible focus states.
Sufficient color contrast.
Reduced-motion support.
Accessible forms and buttons.
Descriptive alt text for all media.
SEO Requirements

The homepage should include:

Optimized title and meta description.
Open Graph and Twitter Card metadata.
Structured data (Organization, LocalBusiness, WebSite).
Optimized headings (H1–H3).
Internal links to services, projects, and knowledge articles.
Image optimization and descriptive filenames.
Video schema where applicable.
Performance Requirements

Target metrics:

Lighthouse Performance: 95+
Accessibility: 100
Best Practices: 100
SEO: 100

Technical requirements:

Lazy-load images and videos.
Use responsive image formats (AVIF, WebP).
Cache homepage content in Redis.
Queue image conversions.
Minimize layout shifts (CLS).
Optimize Largest Contentful Paint (LCP).

---
# Company Module

---

# Purpose

The Company module exists to establish credibility, communicate the company's identity, and demonstrate why prospective clients should trust Zytech Contractors with their projects.

Rather than serving as a simple "About Us" page, this module should reinforce professionalism, transparency, and technical expertise.

Every section should answer a question that a potential client is already asking.

Examples include:

- Who are you?
- Can I trust you?
- Why should I choose you?
- Are you experienced?
- Are you qualified?
- Have you delivered similar projects?

The Company module should reduce hesitation while strengthening confidence.

---

# Business Goals

The Company module should:

- Build trust.
- Humanize the company.
- Differentiate Zytech from competitors.
- Highlight company expertise.
- Showcase experience.
- Demonstrate professionalism.
- Reinforce company values.
- Increase quotation requests.

---

# Success Metrics

The effectiveness of this module should be measured using:

- Average time spent on page.
- Team profile views.
- Contact CTA clicks.
- Quote CTA clicks.
- Scroll depth.
- Returning visitors.
- Conversion rate after viewing the Company page.

---

# Information Architecture

Company

↓

Hero

↓

Our Story

↓

Mission & Vision

↓

Core Values

↓

Leadership Team

↓

Why Choose Zytech

↓

Certifications & Memberships

↓

Awards & Recognition

↓

Construction Process

↓

Frequently Asked Questions

↓

Call To Action

---

# Visitor Journey

Visitor

↓

Learns who the company is

↓

Understands the company's values

↓

Views leadership

↓

Builds confidence

↓

Explores projects

↓

Requests quotation

---

# Company Hero

Purpose

Introduce the company with confidence.

Components

- Large Hero Image
- Company Headline
- Supporting Description
- CTA
- Company Statistics

Example headline

```
Building Trust Through Quality Construction
```

Primary CTA

```
Request a Consultation
```

Secondary CTA

```
Explore Our Projects
```

---

# Our Story

Purpose

Communicate the company's journey.

Should include:

- Company origins.
- Growth.
- Major milestones.
- Philosophy.
- Future vision.

Avoid long chronological paragraphs.

Instead, tell a concise story centered around customers and quality.

---

# Mission

Purpose

Explain why the company exists.

Mission statements should focus on customer outcomes rather than internal goals.

Example themes

- Quality
- Reliability
- Innovation
- Sustainability
- Customer Satisfaction

---

# Vision

Purpose

Communicate the company's long-term ambition.

The vision should inspire confidence and reflect leadership within Kenya's construction industry.

---

# Core Values

Purpose

Define the principles that guide every project.

Suggested values

- Integrity
- Excellence
- Safety
- Innovation
- Accountability
- Collaboration
- Sustainability

Each value should include:

- Icon
- Title
- Short description

---

# Leadership Team

Purpose

Humanize the company.

Each team member should include:

- Professional Photo
- Full Name
- Position
- Qualifications
- Biography
- Years of Experience
- LinkedIn (optional)

Future

- Professional Certifications
- Publications
- Speaking Engagements

---

# Why Choose Zytech

Purpose

Differentiate the company.

Suggested cards

✓ Experienced Professionals

✓ Transparent Communication

✓ Modern Construction Methods

✓ Regulatory Compliance

✓ Quality Assurance

✓ Timely Delivery

✓ Customer-Centered Approach

✓ End-to-End Service

---

# Certifications & Memberships

Purpose

Provide objective trust signals.

Display

- Professional Licenses
- Regulatory Approvals
- Industry Memberships
- Business Registration
- Insurance Coverage
- Safety Certifications

Each certification should include:

- Logo
- Issuing Organization
- Issue Date
- Expiry Date (if applicable)

Future

Verification links.

---

# Awards & Recognition

Purpose

Highlight external validation.

Examples

Industry awards

Recognition

Media mentions

Professional achievements

Future

Press releases.

---

# Construction Philosophy

Purpose

Explain how Zytech approaches every project.

Topics

Planning

Communication

Quality Control

Safety

Customer Collaboration

Attention to Detail

Continuous Improvement

---

# Construction Process

Purpose

Educate potential clients on how projects are delivered.

Steps

1. Consultation

↓

2. Site Assessment

↓

3. Design

↓

4. Estimation

↓

5. Approvals

↓

6. Construction

↓

7. Quality Inspection

↓

8. Project Handover

Interactive timeline preferred.

---

# Frequently Asked Questions

Purpose

Reduce customer uncertainty.

Examples

- How long does a project take?
- Do you provide approvals?
- Can you work outside Nairobi?
- How are quotations prepared?
- What payment methods do you accept?
- Do you offer warranties?

Future

Searchable FAQ.

---

# Call To Action

Headline

```
Let's Build Something Extraordinary Together
```

Supporting Text

CTA

```
Request Your Free Consultation
```

Secondary CTA

```
View Our Projects
```

---

# SEO Requirements

The Company module should include:

- Organization schema.
- LocalBusiness schema.
- Breadcrumb schema.
- Team member structured data where applicable.
- Optimized metadata.
- Internal links to Services, Projects, and Contact.
- Optimized images.
- Open Graph metadata.

---

# Accessibility Requirements

- Semantic HTML.
- Accessible navigation.
- Keyboard support.
- Alt text for all images.
- Screen reader compatibility.
- Visible focus indicators.
- Reduced-motion support.

---

# Analytics

Track:

- Company page views.
- Team profile clicks.
- CTA conversions.
- FAQ interactions.
- Scroll depth.
- Time on page.
- Internal navigation paths.

---

# Business Rules

- At least one company administrator must exist.
- Company profile information should be managed from the CMS.
- Certifications may have expiry dates.
- Team members may be hidden without deletion.
- Awards should be displayed chronologically.
- FAQs should be categorized.

---

# Domain Events

Examples

CompanyProfileUpdated

MissionUpdated

VisionUpdated

TeamMemberAdded

TeamMemberUpdated

CertificationAdded

CertificationExpired

AwardPublished

CompanySettingsUpdated

These events should refresh Redis caches, update search indexes where necessary, and broadcast changes to connected administrative users.

---

# Engineering Readiness

| Component | Status |
|------------|---------|
| Business Specification | ✅ |
| UI Specification | ✅ |
| UX Specification | ✅ |
| SEO Specification | ✅ |
| Analytics Specification | ✅ |
| Database Schema | ☐ |
| Livewire Components | ☐ |
| Filament Resources | ☐ |
| Events | ☐ |
| Policies | ☐ |
| Tests | ☐ |
| Documentation | ✅ |


---

# Services Module

---

# Purpose

The Services module exists to communicate the company's expertise, educate prospective clients about the solutions offered, and guide visitors toward requesting a quotation or consultation.

Rather than functioning as a simple list of services, this module should demonstrate Zytech Contractors' technical capabilities, showcase completed work related to each service, and answer common customer questions.

Each service page should act as a landing page capable of ranking independently in search engines and converting visitors into qualified leads.

---

# Business Goals

The Services module should:

- Clearly communicate every service offered.
- Educate prospective clients.
- Demonstrate technical expertise.
- Increase quotation requests.
- Improve SEO visibility.
- Cross-promote completed projects.
- Strengthen customer confidence.
- Support future online consultations.

---

# Success Metrics

Measure the effectiveness of the Services module using:

- Service page views.
- Average time on service pages.
- Quote requests originating from service pages.
- CTA click-through rate.
- Related project views.
- FAQ interactions.
- Organic search traffic.
- Scroll depth.
- Conversion rate per service.

---

# Information Architecture

Services

↓

Services Overview

↓

Individual Service

↓

Service Process

↓

Benefits

↓

Related Projects

↓

Frequently Asked Questions

↓

Related Knowledge Articles

↓

Call To Action

---

# Visitor Journey

Visitor

↓

Views Services

↓

Selects a Service

↓

Understands the Process

↓

Views Related Projects

↓

Reads FAQs

↓

Requests Consultation

---

# Services Overview

Purpose

Provide visitors with a clear overview of the company's capabilities.

Display services as premium interactive cards.

Each card should include:

- Service icon
- Service title
- Short summary
- Hero image
- Learn More CTA

Hover interactions should subtly communicate professionalism.

---

# Individual Service Page

Every service should have its own dedicated landing page.

Example services include:

- Interior Design
- Exterior Design
- Property Sketching
- Plan Estimation
- Plan Approvals

Future services can be added without requiring code changes.

---

# Service Hero

Purpose

Immediately communicate what the service provides.

Components

- Hero image or short looping video.
- Service title.
- Short description.
- Primary CTA.
- Related statistics.

Example CTA

```
Request a Consultation
```

Secondary CTA

```
View Related Projects
```

---

# Service Description

Purpose

Explain:

- What the service is.
- Who it is for.
- Why it matters.
- Expected outcomes.

Content should be educational rather than overly promotional.

---

# Benefits Section

Purpose

Help visitors understand the value of the service.

Example benefits:

Interior Design

- Functional layouts.
- Improved aesthetics.
- Better space utilization.
- Increased property value.

Exterior Design

- Strong first impressions.
- Weather-resistant finishes.
- Enhanced curb appeal.

Plan Estimation

- Accurate budgeting.
- Reduced financial surprises.
- Better project planning.

Each benefit should include:

- Icon.
- Title.
- Supporting explanation.

---

# Service Process

Purpose

Explain how Zytech delivers the service.

Example workflow

1. Initial Consultation

↓

2. Site Assessment

↓

3. Design & Planning

↓

4. Cost Estimation

↓

5. Client Approval

↓

6. Execution

↓

7. Quality Review

↓

8. Completion

Interactive timeline preferred.

---

# Related Projects

Purpose

Demonstrate proven experience.

Display:

- Featured image.
- Project title.
- Location.
- Completion year.
- Services involved.

Allow filtering by project category.

---

# Before & After Showcase

Where applicable.

Applicable services include:

- Interior Design.
- Exterior Design.
- Renovations.

Features

- Interactive comparison slider.
- Fullscreen mode.
- Captions.

---

# Frequently Asked Questions

Purpose

Answer common customer concerns.

Example questions

Interior Design

- How long does interior design take?
- Can you redesign occupied homes?
- Do you supply materials?

Plan Approvals

- How long do approvals take?
- Which authorities are involved?
- What documents are required?

FAQs should be searchable.

---

# Related Knowledge Articles

Purpose

Educate visitors while improving SEO.

Example

Interior Design

↓

Articles

- Choosing Interior Finishes
- Interior Design Trends
- Budget-Friendly Renovations

Each article should display:

- Featured image.
- Category.
- Reading time.
- Publication date.

---

# Call To Action

Headline

```
Ready to Start Your Project?
```

Supporting Text

Encourage visitors to discuss their project with the team.

Primary CTA

```
Request a Free Consultation
```

Secondary CTA

```
Explore Our Projects
```

---

# Search Requirements

Visitors should be able to search services by:

- Name.
- Keywords.
- Category.
- Related projects.

Future

AI-powered semantic search.

---

# SEO Requirements

Each service page should include:

- Unique title.
- Meta description.
- Open Graph metadata.
- Breadcrumbs.
- FAQ Schema.
- Service Schema.
- LocalBusiness Schema.
- Canonical URL.
- Optimized headings.
- Internal links.
- Optimized media.

---

# Accessibility Requirements

- Semantic HTML.
- Accessible navigation.
- Keyboard support.
- Descriptive image alt text.
- Accessible comparison sliders.
- Reduced-motion support.

---

# Analytics

Track:

- Service page visits.
- CTA clicks.
- Related project interactions.
- FAQ usage.
- Scroll depth.
- Conversion rate.
- Search terms.

---

# Business Rules

- Every service must have a unique slug.
- Every service must include a summary.
- Services may be published or unpublished.
- Every published service should contain at least one hero image.
- Services may be linked to multiple projects.
- Services may contain multiple FAQs.
- Services may reference multiple knowledge articles.
- Services should support future multilingual content.

---

# Permissions

Visitors

✓ View services

Clients

✓ View services

Staff

✓ Create services

✓ Update services

Administrators

✓ Full management

---

# Domain Events

Examples

ServiceCreated

ServiceUpdated

ServicePublished

ServiceArchived

ServiceFeatured

ServiceViewed

These events should trigger:

- Redis cache invalidation.
- Search index updates.
- Analytics aggregation.
- Homepage refresh.
- Related project cache refresh.

---

# Future Expansion

Future versions of the Services module may include:

- Online consultations.
- Appointment scheduling.
- Service pricing calculators.
- Cost estimators.
- AI recommendations.
- Downloadable service brochures.
- 3D visualizations.
- Virtual consultations.
- Interactive room planners.
- BIM model previews.

---

# Engineering Readiness

| Component | Status |
|------------|---------|
| Business Specification | ✅ |
| UI Specification | ✅ |
| UX Specification | ✅ |
| SEO Specification | ✅ |
| Analytics Specification | ✅ |
| Database Schema | ☐ |
| Livewire Components | ☐ |
| Filament Resources | ☐ |
| Policies | ☐ |
| Events | ☐ |
| Listeners | ☐ |
| Jobs | ☐ |
| Notifications | ☐ |
| Tests | ☐ |
| Documentation | ✅ |


---

# Projects Module

---

# Purpose

The Projects module is the flagship feature of the Zytech Contractors Platform.

It serves as the company's digital portfolio, demonstrating technical expertise, construction quality, design capability, and successful project delivery.

Unlike a traditional gallery, each project should tell a complete story—from concept through completion—allowing visitors to understand not only what was built, but how it was delivered.

The module should inspire confidence, educate prospective clients, and convert interest into quotation requests.

Projects are the strongest proof of the company's capabilities.

---

# Business Objectives

The Projects module exists to achieve the following objectives.

## Build Trust

Potential clients should see evidence of completed work rather than relying on marketing claims.

---

## Demonstrate Experience

Highlight experience across multiple sectors including:

- Residential
- Commercial
- Industrial
- Hospitality
- Institutional
- Renovations
- Interior Design
- Exterior Design

---

## Generate Qualified Leads

Every project should naturally encourage visitors to request a quotation or consultation.

---

## Improve SEO

Each project becomes an indexed landing page capable of attracting search traffic.

Example searches:

- Residential contractor in Nairobi
- Interior design in Kenya
- Warehouse construction
- Building renovation contractor

---

## Showcase Technical Excellence

Demonstrate:

- Design quality
- Construction standards
- Project management
- Attention to detail

---

## Support Future Client Portal

Projects will eventually become collaborative workspaces where clients can monitor progress, documents, reports, and communication.

---

# Success Metrics

The effectiveness of the Projects module should be measured through:

- Project page views.
- Average engagement time.
- Gallery interactions.
- Video plays.
- Before/After interactions.
- Map interactions.
- Timeline interactions.
- Related project navigation.
- Quote requests originating from projects.
- Organic search traffic.
- Returning visitors.

---

# Business Principles

Every published project should answer six questions.

## What was built?

Visitors should understand the project immediately.

---

## Why was it built?

Explain the client's objectives.

---

## How was it delivered?

Demonstrate the company's process and expertise.

---

## What challenges existed?

Highlight engineering, architectural, or logistical challenges.

---

## How were they solved?

Explain the solutions implemented.

---

## What was achieved?

Show measurable outcomes.

Examples:

- Improved functionality
- Modernized spaces
- Increased property value
- Faster construction
- Sustainable outcomes

---

# Information Architecture

Projects

↓

Projects Listing

↓

Project Details

↓

Gallery

↓

Videos

↓

Timeline

↓

Before & After

↓

Location

↓

Related Projects

↓

Knowledge Articles

↓

Quote Request

---

# Visitor Journey

Visitor

↓

Discovers Project

↓

Views Images

↓

Watches Video

↓

Reads Story

↓

Explores Timeline

↓

Views Related Projects

↓

Requests Consultation

The experience should feel like reading a professional case study rather than browsing a gallery.

---

# Project Categories

Every project belongs to one primary category.

Initial categories:

- Residential
- Commercial
- Industrial
- Hospitality
- Office
- Educational
- Healthcare
- Government
- Renovation
- Mixed Use

Future categories may be added through the CMS without code changes.

---

# Project Types

Projects may also include one or more types.

Examples:

- New Construction
- Renovation
- Interior Design
- Exterior Design
- Fit Out
- Extension
- Structural Repairs
- Landscaping

Types allow richer filtering than categories alone.

---

# Project Status

Public project statuses:

- Featured
- Completed
- Ongoing
- Archived

Internal statuses (future):

- Planning
- Design
- Approvals
- Construction
- Inspection
- Handover
- Closed

Only published statuses should appear publicly.

---

# Project Metadata

Every project should contain rich metadata.

Core information includes:

- Project Title
- Slug
- Summary
- Full Description
- Client Type (optional)
- Completion Year
- County
- Town
- GPS Coordinates
- Services Provided
- Project Category
- Project Type
- Construction Area
- Duration
- Featured Flag
- SEO Metadata

Metadata should support filtering, search, analytics, and future reporting.

---

# User Stories

### Visitor

As a visitor,

I want to browse completed projects,

so that I can evaluate the company's experience.

---

As a visitor,

I want to filter projects,

so that I can quickly find work similar to my own needs.

---

As a visitor,

I want to watch project videos,

so that I can better understand construction quality.

---

As a visitor,

I want to view before-and-after comparisons,

so that I can appreciate the transformation.

---

As a visitor,

I want to locate projects on a map,

so that I can see the regions where the company operates.

---

As a client,

I want to view projects associated with my account,

so that I can monitor completed work.

(Future Release)

---

# Business Rules

- Every project must belong to at least one service.
- Every published project must include a hero image.
- Every project must have a unique slug.
- Featured projects must also be published.
- Projects may belong to multiple services.
- Projects may contain multiple media assets.
- Projects may contain multiple videos.
- Projects may contain multiple documents.
- Projects may contain multiple timeline milestones.
- Projects may contain multiple tags.
- Archived projects must not appear in public listings.
- Related projects should be generated automatically based on shared categories, services, and tags.

---

# Engineering Readiness

| Component | Status |
|-----------|--------|
| Business Specification | ✅ |
| Product Requirements | ✅ |
| User Stories | ✅ |
| Business Rules | ✅ |
| Database Schema | ☐ |
| Livewire Components | ☐ |
| Filament Resources | ☐ |
| Events | ☐ |
| Policies | ☐ |
| Tests | ☐ |

---

