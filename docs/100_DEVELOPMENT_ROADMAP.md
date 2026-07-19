# 100_DEVELOPMENT_ROADMAP.md

> **Enterprise Development Roadmap**
>
> Complete implementation guide for building the Zytech Contractors Platform from inception to production.

---

# Table of Contents

- [Project Vision](#project-vision)
- [Development Philosophy](#development-philosophy)
- [Overall Development Strategy](#overall-development-strategy)
- [Technology Stack](#technology-stack)
- [Architecture Principles](#architecture-principles)
- [Project Timeline](#project-timeline)
- [Development Phases](#development-phases)
- [Implementation Playbook](#implementation-playbook)
- [Module Dependencies](#module-dependencies)
- [Definition of Done](#definition-of-done)
- [Production Readiness Checklist](#production-readiness-checklist)
- [Future Expansion Roadmap](#future-expansion-roadmap)

---

# Project Vision

The Zytech Platform is **not** a traditional company website.

It is an enterprise platform designed to support the complete customer journey—from marketing and quotation requests to project execution, customer collaboration, and future business operations.

The platform consists of:

- Public Marketing Website
- Company Management
- Service Management
- Project Portfolio
- Knowledge Centre
- Quotation System
- Client Portal
- Media Management
- Internal Administration
- Reporting & Analytics
- Future ERP Modules
- Future CRM Modules
- Future Mobile Applications

Every architectural decision should support long-term scalability and avoid unnecessary rewrites.

---

# Development Philosophy

Development follows a structured, architecture-first methodology.

```text
Business Requirements
        │
        ▼
Architecture
        │
        ▼
Database
        │
        ▼
Backend
        │
        ▼
Frontend
        │
        ▼
Optimization
        │
        ▼
Testing
        │
        ▼
Documentation
        │
        ▼
Deployment
```

The following principles govern every feature:

- Business Value First
- Architecture Before Features
- Security by Default
- Performance by Design
- Test Everything
- Document Everything
- Build for Reuse
- Keep Components Small
- Keep Business Logic Modular

No feature is considered complete until it has:

- ✅ Tests
- ✅ Documentation
- ✅ Authorization
- ✅ Activity Logging
- ✅ Events
- ✅ Notifications (if applicable)
- ✅ Redis Optimization
- ✅ Queue Support (if required)

---

# Overall Development Strategy

```text
Planning
    │
    ▼
Architecture
    │
    ▼
Environment Setup
    │
    ▼
Authentication
    │
    ▼
Administration Panel
    │
    ▼
Business Domains
    │
    ▼
Public Website
    │
    ▼
Client Portal
    │
    ▼
Optimization
    │
    ▼
Testing
    │
    ▼
Deployment
```

The project is developed incrementally.

Each phase builds upon the previous one.

No phase should begin until the previous phase satisfies its Definition of Done.

---

# Technology Stack

| Layer | Technology |
|----------|------------|
| Language | PHP 8.4 |
| Framework | Laravel 13 |
| Admin Panel | Filament 5 |
| Frontend | Livewire 4 |
| JavaScript | Alpine.js |
| CSS | Bootstrap 5 (customized) |
| Database | PostgreSQL 16 |
| Cache | Redis |
| Queue | Redis |
| Broadcasting | Laravel Reverb |
| Permissions | Spatie Permission |
| Media | Spatie Media Library |
| Search | PostgreSQL Full Text Search |
| Web Server | Apache2 |
| Testing | Pest / PHPUnit |
| Package Manager | Composer |
| Version Control | Git |
| Repository | GitHub |

---

# Architecture Principles

The project follows a modular, domain-driven architecture.

Every feature belongs to a domain.

```text
Company
│
├── Services
├── Projects
├── Clients
├── Quotations
├── Knowledge Centre
├── Media
├── Authentication
├── Configuration
├── Notifications
└── System
```

Each domain owns:

- Models
- Policies
- DTOs
- Repositories
- Services
- Actions
- Events
- Listeners
- Notifications
- Livewire Components
- Filament Resources
- Tests

---

# Project Timeline

```text
Phase 0
Environment

↓

Phase 1
Core Architecture

↓

Phase 2
Authentication

↓

Phase 3
Configuration

↓

Phase 4
Company

↓

Phase 5
Services

↓

Phase 6
Projects

↓

Phase 7
Media

↓

Phase 8
Knowledge Centre

↓

Phase 9
Quotations

↓

Phase 10
Clients

↓

Phase 11
Public Website

↓

Phase 12
Client Portal

↓

Phase 13
Messaging

↓

Phase 14
Search

↓

Phase 15
SEO

↓

Phase 16
Performance

↓

Phase 17
Testing

↓

Phase 18
Deployment

↓

Phase 19
Future Enhancements
```

---

# Development Phases

Each phase contains:

- Objective
- Deliverables
- Dependencies
- Packages
- Database Changes
- Backend Tasks
- Frontend Tasks
- Testing
- Documentation
- Acceptance Criteria

---

# Phase 0 — Environment Setup

## Objective

Create a professional local development environment.

---

## Deliverables

- Laravel installed
- PostgreSQL configured
- Redis configured
- Reverb configured
- Queue configured
- Filament installed
- Livewire installed
- Spatie packages installed
- UUID strategy configured
- Apache configured
- Git repository initialized

---

## Packages

- Laravel
- Filament
- Livewire
- Laravel Reverb
- Redis
- Spatie Permission
- Spatie Media Library
- Laravel Pint
- Laravel Debugbar
- Laravel Telescope (Local)

---

## Configuration

Configure:

- PostgreSQL
- Redis
- Queue
- Cache
- Session
- Broadcasting
- Mail
- Filesystem
- Timezone
- UUIDs

---

## Acceptance Criteria

- [x] Laravel boots successfully
- [ ] PostgreSQL connected *(you are configuring)*
- [ ] Redis connected *(you are configuring)*
- [ ] Queue worker operational *(pending Redis)*
- [ ] Reverb operational *(pending Redis + `php artisan reverb:start`)*
- [x] Filament accessible
- [x] Tests passing *(DB-dependent UUID test skipped until PostgreSQL is ready)*
- [x] Spatie Permission + Media Library installed
- [x] Telescope + Debugbar installed
- [x] UUID strategy configured
- [x] Git repository initialized
- [x] `.env.example` prepared for pgsql/redis/reverb

---

# Implementation Playbook

Every development phase follows the exact same workflow.

```text
Requirement

↓

Migration

↓

Enums

↓

Models

↓

Factories

↓

Seeders

↓

Policies

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

Livewire Components

↓

Filament Resources

↓

Frontend Pages

↓

Tests

↓

Documentation

↓

Performance Review

↓

Code Review

↓

Complete
```

No steps should be skipped unless explicitly documented.

---

# Module Dependencies

```text
Authentication
        │
        ▼
Users
        │
        ▼
Roles & Permissions
        │
        ▼
Configuration
        │
        ▼
Company
        │
        ▼
Services
        │
        ▼
Projects
        │
        ▼
Media
        │
        ▼
Knowledge Centre
        │
        ▼
Quotations
        │
        ▼
Clients
        │
        ▼
Public Website
        │
        ▼
Client Portal
```

Dependencies should always flow downward.

Avoid circular dependencies between domains.

---

# Definition of Done

A feature is considered complete only if all of the following are satisfied.

- [ ] Database complete
- [ ] UUID support
- [ ] Policies implemented
- [ ] DTOs implemented
- [ ] Repository created
- [ ] Services implemented
- [ ] Actions extracted
- [ ] Events dispatched
- [ ] Listeners registered
- [ ] Notifications configured
- [ ] Activity logged
- [ ] Redis caching implemented
- [ ] Queue integration complete
- [ ] Reverb broadcasting configured
- [ ] Responsive UI
- [ ] Mobile friendly
- [ ] Accessibility reviewed
- [ ] Feature tests passing
- [ ] Documentation updated

---

# Production Readiness Checklist

Before deployment verify:

- [ ] Environment variables configured
- [ ] APP_DEBUG disabled
- [ ] HTTPS configured
- [ ] Redis operational
- [ ] Queue workers supervised
- [ ] Reverb running
- [ ] Apache optimized
- [ ] PostgreSQL backups enabled
- [ ] Activity logging operational
- [ ] Monitoring configured
- [ ] Security headers enabled
- [ ] SSL certificate installed
- [ ] Scheduled tasks configured

---

# Future Expansion Roadmap

The platform architecture has been designed to support future modules without requiring significant restructuring.

Planned expansions include:

- ERP
- CRM
- Procurement
- Inventory
- HR
- Payroll
- Accounting Integration
- Vendor Portal
- Employee Portal
- Mobile Applications
- AI Assistant
- AI Knowledge Search
- Online Payments
- Meeting Scheduler
- Customer Support Centre
- Business Intelligence Dashboards

Each future module should follow the same architectural standards defined throughout this documentation.

# =============================================================================
# PHASE 1
# CORE ARCHITECTURE
# =============================================================================

---

# Phase 1 — Core Architecture

## Objective

Establish the engineering foundation that every future feature will build upon.

No business modules should be developed before this phase is completed.

This phase defines:

- Folder structure
- Coding standards
- Base architecture
- Shared abstractions
- Reusable components
- Domain conventions

---

# Estimated Duration

1 Week

---

# Dependencies

- Phase 0 Complete

---

# Deliverables

- Domain Structure
- Base Models
- UUID Support
- Base Repository
- Base Service
- Base Action
- Base DTO
- Base Policy
- Base Event
- Base Listener
- Base Notification
- Base Livewire Component
- Base Filament Resource
- Shared Traits
- Helper Classes
- Support Classes
- Global Enums
- Global Exceptions

---

# Folder Structure

app/

Domain/

Shared/

Actions/

DTOs/

Events/

Exceptions/

Listeners/

Models/

Notifications/

Policies/

Repositories/

Services/

Support/

Traits/

ValueObjects/

Enums/

Contracts/

Interfaces/

Console/

---

# Tasks

## Domain Structure

Create

- Company
- Services
- Projects
- Clients
- Quotations
- Knowledge Centre
- Media
- Authentication
- Configuration
- Notifications
- System

---

## Shared Components

Create

BaseModel

BaseRepository

BaseService

BaseAction

BaseDTO

BasePolicy

BaseNotification

BaseLivewireComponent

BaseResource

---

## UUID Strategy

Every model uses

```php
HasUuids
```

Never use incremental IDs.

Every public URL should use UUIDs.

---

## Enums

Create global enums.

Examples

ProjectStatus

QuotationStatus

ApprovalStatus

VisibilityStatus

MediaCollection

NotificationType

UserType

RoleType

---

## Traits

Create reusable traits.

Examples

HasUuid

HasSlug

HasActivity

HasSeo

HasMedia

HasStatus

HasPublishedState

HasAuditTrail

HasOwnership

---

## Support Classes

Create

Slug Generator

Image Optimizer

SEO Manager

Breadcrumb Generator

Navigation Builder

Menu Builder

Permission Helper

Cache Helper

Search Helper

---

## Value Objects

Create

Money

Phone

Email

Coordinates

Address

GeoPoint

WorkingHours

Measurement

---

## Events

Create base events.

BusinessEvent

BroadcastEvent

SystemEvent

---

## Notifications

Create base notifications.

EmailNotification

DatabaseNotification

BroadcastNotification

---

## Activity Logging

Configure Spatie Activitylog.

Log

Create

Update

Delete

Restore

Login

Logout

Role Assignment

Permission Assignment

Media Upload

Quotation Approval

Project Updates

---

## Redis

Create cache abstraction.

Never access Cache facade directly.

Use dedicated services.

---

## Reverb

Configure

Broadcast channels

Private channels

Presence channels

Authorization

---

## Queues

Configure

Default Queue

Mail Queue

Media Queue

Search Queue

Broadcast Queue

Notification Queue

---

# Testing

Verify

- UUID generation
- Base classes
- Repository pattern
- Services
- Events
- Queue configuration
- Redis configuration
- Reverb configuration

---

# Documentation

Update

Architecture

Development

README

Agent Rules

---

# Definition of Done

- [ ] Folder structure complete
- [ ] Base classes complete
- [ ] UUID strategy complete
- [ ] Redis configured
- [ ] Reverb configured
- [ ] Queue configured
- [ ] Shared traits complete
- [ ] Shared helpers complete
- [ ] Shared enums complete
- [ ] Tests passing
- [ ] Documentation updated

---

# =============================================================================
# PHASE 2
# AUTHENTICATION & IDENTITY
# =============================================================================

# Objective

Create a secure authentication and authorization system that supports:

- Administrators
- Employees
- Clients

The authentication layer must be scalable enough to support future mobile applications and APIs.

---

# Deliverables

Authentication

Registration

Login

Logout

Password Reset

Email Verification

Remember Me

User Profiles

Roles

Permissions

Policies

Sessions

Audit Logs

MFA Foundation

Account Locking

Trusted Devices

Client Login

Admin Login

---

# Packages

Laravel Starter Kit

Spatie Permission

Laravel Sanctum (future API)

Laravel Reverb

Redis

---

# Database

Tables

users

roles

permissions

model_has_roles

model_has_permissions

password_reset_tokens

sessions

personal_access_tokens

---

# Models

User

Role

Permission

Session

---

# Services

AuthenticationService

RegistrationService

ProfileService

SessionService

PermissionService

RoleService

---

# Actions

RegisterUser

AuthenticateUser

LogoutUser

AssignRole

AssignPermission

UpdateProfile

ResetPassword

VerifyEmail

LockAccount

UnlockAccount

---

# Events

UserRegistered

UserLoggedIn

UserLoggedOut

PasswordReset

EmailVerified

RoleAssigned

PermissionAssigned

AccountLocked

---

# Listeners

SendWelcomeEmail

LogLogin

LogLogout

CreateNotifications

BroadcastAuthenticationEvent

---

# Livewire Components

Login

Register

Forgot Password

Reset Password

Profile

Security Settings

Sessions

Account Settings

---

# Filament

User Resource

Role Resource

Permission Resource

Profile Resource

Session Resource

---

# Security

Password Hashing

Email Verification

Rate Limiting

CSRF

Session Protection

Authorization Policies

Remember Tokens

---

# Redis

Cache

Permissions

Roles

User Preferences

Navigation

---

# Reverb

Realtime login notifications

Realtime session revocation

---

# Testing

Authentication

Authorization

Policies

Password Reset

Roles

Permissions

Email Verification

Rate Limiting

Session Handling

---

# Definition of Done

- [ ] Authentication complete
- [ ] Roles complete
- [ ] Permissions complete
- [ ] Policies complete
- [ ] Activity logs working
- [ ] Tests passing
- [ ] Documentation updated

---

# =============================================================================
# PHASE 3
# CONFIGURATION DOMAIN
# =============================================================================

# Objective

Build a centralized configuration system that allows administrators to manage
application-wide settings without modifying code or environment files.

Every configurable aspect of the platform should be manageable through Filament
where appropriate.

---

# Estimated Duration

3–5 Days

---

# Dependencies

- Phase 2 Complete

---

# Deliverables

- System Settings
- Company Branding
- SEO Configuration
- Contact Information
- Social Media Links
- Email Settings
- Analytics Settings
- Storage Configuration
- Feature Flags
- Maintenance Mode
- Homepage Configuration
- Navigation Management
- Footer Configuration

---

# Database

Tables

settings

setting_groups

feature_flags

navigation_menus

navigation_items

---

# Models

Setting

SettingGroup

FeatureFlag

NavigationMenu

NavigationItem

---

# Enums

SettingType

SettingGroupType

FeatureStatus

NavigationLocation

---

# DTOs

SettingData

NavigationData

BrandingData

SEOData

---

# Repositories

SettingRepository

NavigationRepository

FeatureFlagRepository

---

# Services

ConfigurationService

BrandingService

NavigationService

SEOConfigurationService

FeatureFlagService

---

# Actions

UpdateSettings

UpdateBranding

PublishNavigation

EnableFeature

DisableFeature

ClearConfigurationCache

---

# Events

SettingsUpdated

BrandingUpdated

NavigationUpdated

FeatureEnabled

FeatureDisabled

---

# Listeners

ClearRedisConfigurationCache

BroadcastConfigurationChanged

LogConfigurationChange

---

# Policies

SettingPolicy

NavigationPolicy

FeatureFlagPolicy

---

# Livewire Components

General Settings

Brand Settings

SEO Settings

Navigation Builder

Feature Flags

Footer Builder

Homepage Settings

---

# Filament Resources

Settings Resource

Navigation Resource

Feature Flags Resource

---

# Redis Strategy

Cache

- Settings
- Branding
- Navigation
- Feature Flags

Invalidate only affected cache keys.

Never clear the entire cache.

---

# Queue Strategy

Queue

- Navigation regeneration
- Sitemap regeneration
- Cache warming

---

# Reverb

Broadcast

- Configuration Updated
- Navigation Changed
- Feature Flag Changed

---

# Security

Only Super Administrators may modify system settings.

Every change must be activity logged.

---

# Performance

Cache all configuration data.

Avoid repeated database lookups.

---

# Testing

- Settings CRUD
- Navigation Builder
- Feature Flags
- Authorization
- Cache Invalidation

---

# Documentation

Update

- Architecture
- Configuration Guide
- README

---

# Definition of Done

- [ ] Settings manageable from Filament
- [ ] Navigation builder operational
- [ ] Branding configurable
- [ ] SEO configurable
- [ ] Feature flags operational
- [ ] Redis cache working
- [ ] Tests passing
- [ ] Documentation updated

---

# =============================================================================
# PHASE 4
# COMPANY DOMAIN
# =============================================================================

# Objective

Develop the Company Domain, which serves as the authoritative source of all
corporate information displayed across the website and client portal.

No company information should be hardcoded in views.

---

# Estimated Duration

4–6 Days

---

# Dependencies

- Phase 3 Complete

---

# Deliverables

- Company Profile
- Company History
- Mission & Vision
- Leadership Team
- Branch Offices
- Contact Information
- Certifications
- Licenses
- Awards
- Partners
- Clients
- Testimonials
- FAQs

---

# Database

Tables

companies

branches

leadership_members

certifications

awards

partners

testimonials

faqs

company_statistics

---

# Models

Company

Branch

LeadershipMember

Certification

Award

Partner

Testimonial

FAQ

Statistic

---

# Enums

BranchType

ContactType

AwardCategory

CertificationStatus

---

# DTOs

CompanyData

BranchData

LeadershipData

TestimonialData

---

# Repositories

CompanyRepository

BranchRepository

PartnerRepository

---

# Services

CompanyService

BranchService

LeadershipService

PartnerService

TestimonialService

---

# Actions

UpdateCompanyProfile

PublishCompanyProfile

ArchivePartner

PublishTestimonial

UpdateStatistics

---

# Events

CompanyUpdated

BranchCreated

PartnerAdded

TestimonialPublished

CertificationUpdated

---

# Listeners

UpdateHomepageStatistics

ClearCompanyCache

BroadcastCompanyChanges

---

# Policies

CompanyPolicy

BranchPolicy

PartnerPolicy

TestimonialPolicy

---

# Livewire Components

About Us

Leadership

Branches

Partners

Testimonials

FAQs

Company Statistics

---

# Filament Resources

Company Resource

Branch Resource

Leadership Resource

Partner Resource

Certification Resource

Award Resource

Testimonial Resource

FAQ Resource

---

# Redis Strategy

Cache

- Company profile
- Leadership
- Branches
- Testimonials
- Partners
- Statistics

---

# Queue Strategy

Queue

- Image optimization
- Statistics recalculation
- Search indexing

---

# Reverb

Broadcast

- Testimonial Published
- Company Updated
- Statistics Updated

---

# Security

Only authorized staff may update company information.

Every modification must be logged.

---

# Performance

Cache all public company information.

Optimize image delivery.

Lazy-load leadership and testimonial sections where appropriate.

---

# Testing

- CRUD operations
- Policies
- Cache invalidation
- Public rendering
- Media handling

---

# Documentation

Update

- Company Module
- Architecture
- API Documentation

---

# Definition of Done

- [ ] Company information fully dynamic
- [ ] Testimonials manageable
- [ ] Branches manageable
- [ ] Partners manageable
- [ ] Certifications manageable
- [ ] Redis caching operational
- [ ] Tests passing
- [ ] Documentation updated

---

# =============================================================================
# PHASE 5
# MEDIA DOMAIN
# =============================================================================

# Objective

Build a centralized media management system using Spatie Media Library.

The Media Domain will become the single source of truth for all images, videos,
documents, PDFs, floor plans, certificates, brochures and future client uploads.

No domain should manage uploaded files independently.

All uploads must go through the Media Domain.

---

# Estimated Duration

5–7 Days

---

# Dependencies

- Phase 4 Complete

---

# Deliverables

- Media Library
- Image Uploads
- Video Uploads
- Document Uploads
- Responsive Images
- Image Conversions
- Collections
- Media Metadata
- Media Search
- Media Browser
- Drag & Drop Uploads
- Bulk Upload
- Bulk Delete
- Media Usage Tracking

---

# Packages

Spatie Media Library

Intervention Image (if required)

FFMpeg (future)

---

# Database

Tables

media

media_folders

media_tags

media_usages

---

# Models

MediaFolder

MediaTag

MediaUsage

---

# Enums

MediaType

MediaCollection

MediaVisibility

MediaStatus

ConversionType

---

# DTOs

MediaUploadData

MediaMetadata

MediaConversionData

---

# Repositories

MediaRepository

MediaFolderRepository

---

# Services

MediaService

ImageOptimizationService

VideoService

MediaSearchService

MediaUsageService

---

# Actions

UploadMedia

DeleteMedia

GenerateConversions

MoveMedia

TagMedia

OptimizeImage

GenerateResponsiveImages

GenerateThumbnail

---

# Events

MediaUploaded

MediaDeleted

MediaConverted

MediaOptimized

MediaMoved

---

# Listeners

GenerateResponsiveImages

OptimizeUploadedImage

IndexMedia

BroadcastMediaUploaded

ClearMediaCache

---

# Policies

MediaPolicy

FolderPolicy

---

# Livewire Components

Media Browser

Media Picker

Media Upload

Media Gallery

Folder Explorer

Recent Uploads

Media Search

---

# Filament Resources

Media Resource

Folder Resource

Tag Resource

---

# Supported Formats

Images

- JPG
- PNG
- WEBP
- SVG

Documents

- PDF
- DOCX
- XLSX

Videos

- MP4
- MOV

Future

- 360 Images
- Drone Videos
- CAD Drawings
- IFC Models

---

# Media Collections

Company

Projects

Services

Knowledge Centre

Team Members

Testimonials

Clients

Documents

Certificates

Downloads

Gallery

Homepage

Hero

SEO

---

# Image Conversions

Thumbnail

Small

Medium

Large

Hero

Retina

WebP

Future

AVIF

---

# Redis Strategy

Cache

Media metadata

Homepage images

Frequently accessed galleries

Media counts

Folder tree

---

# Queue Strategy

Queue

Image optimization

Responsive image generation

Thumbnail generation

Metadata extraction

Video processing

---

# Reverb

Broadcast

Media Uploaded

Media Deleted

Media Updated

---

# Security

Validate MIME types

Validate extensions

Virus scanning (future)

Maximum upload sizes

Private storage for confidential files

Public storage for website assets

---

# Performance

Lazy loading

Responsive images

Image compression

CDN-ready architecture

Optimized thumbnails

---

# Testing

Upload

Delete

Conversion

Policies

Collections

Search

Optimization

---

# Documentation

Update

Media Guide

Architecture

README

---

# Definition of Done

- [ ] Media Library operational
- [ ] Responsive images generated
- [ ] Conversions working
- [ ] Search operational
- [ ] Redis integrated
- [ ] Queues operational
- [ ] Tests passing
- [ ] Documentation updated

---

# =============================================================================
# PHASE 6
# SERVICES DOMAIN
# =============================================================================

# Objective

Build a fully manageable Services Domain that powers the company's public
service catalogue while supporting SEO, quotations, related projects,
and future client workflows.

The Services Domain acts as both a marketing module and a business module.

---

# Estimated Duration

4–6 Days

---

# Dependencies

- Phase 5 Complete

---

# Deliverables

- Service Management
- Service Categories
- Service Gallery
- Pricing Models
- Related Projects
- Related Articles
- FAQs
- SEO
- Featured Services
- Homepage Integration

---

# Database

Tables

services

service_categories

service_features

service_processes

service_faqs

service_statistics

service_related_projects

---

# Models

Service

ServiceCategory

ServiceFeature

ServiceProcess

ServiceFAQ

ServiceStatistic

---

# Enums

ServiceStatus

ServiceType

PricingModel

Visibility

---

# DTOs

ServiceData

ServiceFeatureData

ServiceProcessData

---

# Repositories

ServiceRepository

CategoryRepository

---

# Services

ServiceService

ServiceSEOService

FeaturedServiceService

---

# Actions

CreateService

PublishService

ArchiveService

FeatureService

UpdatePricing

---

# Events

ServiceCreated

ServicePublished

ServiceUpdated

ServiceArchived

---

# Livewire Components

Service Listing

Service Detail

Related Services

FAQ

Featured Services

---

# Filament Resources

Service Resource

Category Resource

FAQ Resource

---

# Frontend Pages

Services

Individual Service

Service Categories

Related Projects

Quotation CTA

---

# Redis Strategy

Cache

Services

Categories

Featured Services

Homepage blocks

---

# Queue Strategy

SEO generation

Image optimization

Search indexing

---

# Reverb

Broadcast

Service Updated

Featured Service Changed

---

# Performance

Cache category pages

Cache featured services

Lazy-load service galleries

Optimize images

---

# Testing

CRUD

SEO

Policies

Search

Caching

Frontend rendering

---

# Definition of Done

- [ ] Services fully manageable
- [ ] Categories operational
- [ ] SEO complete
- [ ] Homepage integration complete
- [ ] Tests passing
- [ ] Documentation updated

---

# =============================================================================
# PHASE 7
# PROJECTS DOMAIN
# =============================================================================

# Objective

Develop an enterprise-grade Project Management Domain that powers the public
portfolio, showcases Zytech's expertise, tracks project progress, manages
construction milestones, and serves as the foundation for future client
project tracking.

The Projects Domain is the flagship feature of the platform.

Every completed project becomes a digital case study that improves customer
confidence, strengthens SEO, and generates future business.

---

# Estimated Duration

2–3 Weeks

---

# Dependencies

- Phase 6 Complete

---

# Deliverables

- Project Management
- Project Categories
- Project Types
- Project Status
- Featured Projects
- Project Gallery
- Videos
- Before & After Comparisons
- Construction Timeline
- Progress Updates
- Milestones
- Related Services
- Related Articles
- Client Testimonials
- Interactive Kenya Map
- Location Management
- Technologies Used
- Materials Used
- Team Members
- Awards
- Downloads
- SEO
- AI SEO

---

# Database

Tables

projects

project_categories

project_types

project_locations

project_galleries

project_videos

project_timelines

project_milestones

project_progress_updates

project_services

project_articles

project_documents

project_statistics

project_materials

project_technologies

project_team_members

project_testimonials

project_tags

project_awards

---

# Models

Project

ProjectCategory

ProjectType

ProjectLocation

ProjectGallery

ProjectVideo

ProjectTimeline

ProjectMilestone

ProjectProgress

ProjectDocument

ProjectStatistic

ProjectMaterial

ProjectTechnology

ProjectTeamMember

ProjectAward

ProjectTag

---

# Enums

ProjectStatus

ProjectVisibility

ProjectType

ConstructionStage

MilestoneStatus

ProjectPriority

MediaType

---

# DTOs

ProjectData

TimelineData

MilestoneData

GalleryData

LocationData

ProgressUpdateData

---

# Repositories

ProjectRepository

TimelineRepository

GalleryRepository

LocationRepository

StatisticsRepository

---

# Services

ProjectService

TimelineService

GalleryService

ProjectMapService

ProjectSearchService

ProjectSEOService

StatisticsService

FeaturedProjectService

---

# Actions

CreateProject

PublishProject

ArchiveProject

FeatureProject

UpdateTimeline

AddMilestone

UploadGallery

UploadVideo

PublishProgressUpdate

AssignTeam

AssignService

AssignKnowledgeArticle

GenerateCaseStudy

---

# Events

ProjectCreated

ProjectPublished

ProjectCompleted

MilestoneCompleted

GalleryUpdated

VideoUploaded

ProgressPublished

ProjectFeatured

ProjectArchived

---

# Listeners

GenerateSEO

UpdateSearchIndex

BroadcastProjectUpdate

ClearProjectCache

NotifySubscribers

GenerateProjectStatistics

---

# Policies

ProjectPolicy

GalleryPolicy

TimelinePolicy

DocumentPolicy

---

# Livewire Components

Project Listing

Project Details

Featured Projects

Related Projects

Project Timeline

Milestones

Gallery

Video Gallery

Before & After Slider

Interactive Map

Project Filters

Project Search

Project Statistics

Related Services

Related Articles

Testimonials

Downloads

---

# Filament Resources

Project Resource

Category Resource

Timeline Resource

Gallery Resource

Video Resource

Statistics Resource

Material Resource

Technology Resource

Location Resource

Award Resource

---

# Frontend Pages

Projects

Project Details

Featured Projects

Project Categories

Interactive Project Map

Construction Timeline

Case Study

---

# Homepage Integration

Featured Projects

Latest Projects

Recently Completed

Current Projects

Construction Statistics

Interactive Map Preview

Client Testimonials

---

# Rich Filtering

Users should be able to filter projects by:

- Service
- Location
- County
- Completion Year
- Category
- Status
- Building Type
- Budget Range (Future)
- Project Size
- Materials
- Technology Used

---

# Interactive Kenya Map

Display completed projects across Kenya.

Each marker should display:

- Project Name
- Thumbnail
- Service
- Completion Year
- Short Description

Clicking a marker opens the Project Details page.

Future Support

- Clustered markers
- Heat maps
- Regional statistics

---

# Before & After Comparisons

Support interactive image comparison sliders.

Each comparison should include:

- Before Image
- After Image
- Caption
- Description

Multiple comparison sets should be supported.

---

# Construction Timeline

Each project should display a visual timeline.

Example

Planning

↓

Approvals

↓

Site Preparation

↓

Foundation

↓

Structure

↓

Roofing

↓

Electrical

↓

Plumbing

↓

Finishes

↓

Inspection

↓

Completion

Each milestone supports:

- Images
- Videos
- Documents
- Notes
- Completion Date

---

# Progress Updates

Project managers may publish updates.

Updates include:

- Title
- Description
- Photos
- Videos
- Completion %
- Date
- Team Member

Updates appear chronologically.

---

# Gallery

Support

Images

Videos

Drone Footage

Blueprints

360 Images (Future)

Virtual Tours (Future)

---

# Documents

Store

Blueprints

Completion Certificates

Inspection Reports

Project Brochures

Downloads

Documents may be public or private.

---

# Technologies Used

Examples

Reinforced Concrete

Steel Framing

BIM

3D Visualization

Smart Home Systems

Solar Integration

Rainwater Harvesting

---

# Materials

Track materials used.

Examples

Concrete

Steel

Timber

Glass

Tiles

Roofing

Paint

---

# Related Content

Each project may link to:

- Services
- Knowledge Articles
- Team Members
- Testimonials
- Awards

This improves navigation and SEO.

---

# Redis Strategy

Cache

Project Listings

Featured Projects

Homepage Projects

Project Statistics

Interactive Map

Timeline Data

Related Projects

Filters

---

# Queue Strategy

Queue

SEO Generation

Search Indexing

Image Optimization

Video Processing

Statistics Generation

Map Data Generation

PDF Case Study Generation

---

# Reverb

Broadcast

Project Published

Progress Update

Milestone Completed

Gallery Updated

Project Featured

---

# Search

Support search by:

Project Name

Service

County

Town

Client

Technology

Material

Year

Keywords

---

# SEO

Generate

Meta Titles

Descriptions

Schema.org

Breadcrumbs

OpenGraph

Twitter Cards

Canonical URLs

JSON-LD

---

# Accessibility

Every gallery must support:

- Alt Text
- Keyboard Navigation
- Screen Readers
- Focus Indicators

---

# Security

Restrict editing to authorized staff.

Private documents must never be publicly accessible.

Validate every upload.

---

# Performance

Redis cache

Responsive images

Lazy loading

Video optimization

Pagination

Cursor pagination where appropriate

Efficient eager loading

Database indexing

---

# Testing

Project CRUD

Policies

Timeline

Gallery

Video Uploads

Search

Filters

Caching

Map Integration

SEO

Responsive Images

Accessibility

---

# Documentation

Update

Projects Module

Architecture

API

README

---

# Definition of Done

- [ ] Project management complete
- [ ] Interactive map operational
- [ ] Before & After slider complete
- [ ] Timeline operational
- [ ] Gallery complete
- [ ] Video support complete
- [ ] Search operational
- [ ] Redis integrated
- [ ] Queues operational
- [ ] Reverb broadcasting operational
- [ ] SEO complete
- [ ] Accessibility reviewed
- [ ] Tests passing
- [ ] Documentation updated

# =============================================================================
# PHASE 8
# KNOWLEDGE CENTRE DOMAIN
# =============================================================================

# Objective

Develop a professional Knowledge Centre that establishes Zytech Contractors as
an authority in construction, architecture, engineering and interior design.

Unlike a traditional blog, the Knowledge Centre serves as a permanent library
of educational resources that improve SEO, educate customers and generate leads.

It should become one of the largest traffic sources for the platform.

---

# Estimated Duration

2 Weeks

---

# Dependencies

- Phase 7 Complete

---

# Deliverables

- Articles
- Categories
- Tags
- Authors
- Reading Time
- Featured Articles
- Related Projects
- Related Services
- FAQs
- Downloadable Resources
- Search
- AI SEO
- Newsletter Integration
- Article Analytics

---

# Database

Tables

articles

article_categories

article_tags

article_authors

article_sections

article_downloads

article_views

article_comments (Future)

article_related_projects

article_related_services

article_faqs

---

# Models

Article

Category

Tag

Author

ArticleSection

ArticleDownload

ArticleView

ArticleFAQ

---

# Enums

ArticleStatus

Visibility

ReadingLevel

ArticleType

---

# DTOs

ArticleData

AuthorData

CategoryData

SectionData

---

# Repositories

ArticleRepository

CategoryRepository

TagRepository

---

# Services

KnowledgeCentreService

ArticleSEOService

RelatedContentService

ReadingTimeService

NewsletterService

SearchService

---

# Actions

CreateArticle

PublishArticle

ArchiveArticle

FeatureArticle

GenerateReadingTime

GenerateSEO

GenerateSchema

AssignRelatedProjects

AssignRelatedServices

---

# Events

ArticleCreated

ArticlePublished

ArticleUpdated

ArticleArchived

ArticleViewed

---

# Listeners

ClearArticleCache

UpdateSearchIndex

GenerateStructuredData

BroadcastNewArticle

SendNewsletter

---

# Policies

ArticlePolicy

CategoryPolicy

AuthorPolicy

---

# Livewire Components

Knowledge Centre

Article Listing

Article Detail

Featured Articles

Related Articles

Categories

Tags

Search

Popular Articles

Latest Articles

Reading Progress

Newsletter Signup

---

# Filament Resources

Article Resource

Category Resource

Tag Resource

Author Resource

FAQ Resource

---

# Frontend Pages

Knowledge Centre

Article Detail

Categories

Search Results

Author Profile

---

# Article Types

Construction Guides

Architecture Guides

Interior Design

Building Regulations

Cost Estimation

Materials

Planning Approvals

Case Studies

FAQs

Industry News

Maintenance Guides

Project Walkthroughs

Downloads

---

# Rich Content Support

Articles may contain

Images

Videos

PDF Downloads

Tables

Code Blocks (Future)

Callouts

Before & After Images

Interactive Galleries

Project Links

Service Links

Maps

---

# Homepage Integration

Featured Articles

Latest Articles

Most Read

Construction Tips

Project Guides

---

# Related Content

Each article may relate to

Projects

Services

FAQs

Downloads

Other Articles

Authors

Categories

Tags

This creates an interconnected content ecosystem that improves navigation and SEO.

---

# Search

Support search by

Title

Keywords

Category

Tags

Author

Content

Project

Service

---

# SEO

Generate

Meta Titles

Descriptions

Canonical URLs

OpenGraph

Twitter Cards

JSON-LD

Article Schema

Breadcrumbs

XML Sitemap

---

# AI SEO

Automatically generate

Suggested Keywords

Related Questions

Suggested Internal Links

SEO Score

Content Quality Score

Readability Score

Future

AI Content Assistant

---

# Downloads

Support downloadable

PDF Guides

Construction Checklists

Building Templates

Planning Documents

Material Guides

Brochures

---

# Redis Strategy

Cache

Featured Articles

Categories

Homepage Articles

Popular Articles

Search Results

Reading Statistics

---

# Queue Strategy

Queue

SEO generation

Reading time calculation

Search indexing

Newsletter delivery

Image optimization

Analytics

---

# Reverb

Broadcast

Article Published

Featured Article Updated

Newsletter Sent

---

# Analytics

Track

Views

Reading Time

Downloads

Popular Topics

Search Terms

Referral Sources

Conversion Rate

Newsletter Signups

---

# Accessibility

Support

Keyboard navigation

Screen readers

Accessible tables

Alt text

Readable typography

Skip links

---

# Performance

Cache article pages

Lazy-load images

Optimize videos

Responsive media

Efficient search

Database indexing

---

# Security

Only authorized authors may publish.

Drafts must not be publicly accessible.

Validate uploads.

Sanitize rich text.

---

# Testing

Article CRUD

Policies

Search

SEO

Downloads

Media

Analytics

Performance

Accessibility

---

# Documentation

Update

Knowledge Centre

SEO Guide

Architecture

README

---

# Definition of Done

- [ ] Article management complete
- [ ] Categories complete
- [ ] Search operational
- [ ] Related content working
- [ ] SEO complete
- [ ] AI SEO operational
- [ ] Downloads working
- [ ] Redis integrated
- [ ] Queues operational
- [ ] Tests passing
- [ ] Documentation updated

# =============================================================================
# PHASE 9
# SALES & QUOTATION DOMAIN
# =============================================================================

# Objective

Develop a complete quotation management system that converts website visitors
into qualified customers while providing an efficient workflow for the sales team.

The quotation system should support the entire quotation lifecycle from initial
request through approval, negotiation, acceptance and project conversion.

It serves as the bridge between the Public Website and the Client Portal.

---

# Estimated Duration

2–3 Weeks

---

# Dependencies

- Phase 8 Complete

---

# Deliverables

- Quotation Requests
- Sales Leads
- Customer Requirements
- Site Visit Requests
- Cost Estimation
- Quotation Builder
- Revision Management
- PDF Generation
- Email Delivery
- Approval Workflow
- Client Acceptance
- Client Rejection
- Digital Sign-off (Future)
- Project Conversion
- Sales Dashboard
- Analytics

---

# Database

Tables

quotation_requests

quotation_request_services

quotation_request_attachments

sales_leads

lead_sources

site_visits

site_visit_reports

quotations

quotation_items

quotation_sections

quotation_terms

quotation_revisions

quotation_status_history

quotation_documents

quotation_comments

quotation_approvals

quotation_signatures

quotation_emails

quotation_activity_logs

---

# Models

SalesLead

QuotationRequest

Quotation

QuotationItem

QuotationRevision

QuotationSection

QuotationApproval

QuotationDocument

SiteVisit

LeadSource

---

# Enums

LeadStatus

QuotationStatus

ApprovalStatus

RevisionStatus

PriorityLevel

SiteVisitStatus

QuotationType

---

# DTOs

LeadData

QuotationRequestData

QuotationData

QuotationItemData

ApprovalData

SiteVisitData

---

# Repositories

LeadRepository

QuotationRepository

SiteVisitRepository

ApprovalRepository

---

# Services

LeadService

QuotationService

QuotationPDFService

ApprovalService

PricingService

SiteVisitService

SalesAnalyticsService

---

# Actions

CreateLead

QualifyLead

CreateQuotation

GenerateQuotationPDF

EmailQuotation

ApproveQuotation

RejectQuotation

RequestRevision

AcceptQuotation

ScheduleSiteVisit

ConvertQuotationToProject

ArchiveQuotation

---

# Events

LeadCreated

LeadQualified

QuotationCreated

QuotationApproved

QuotationRejected

QuotationAccepted

QuotationRevised

QuotationExpired

SiteVisitScheduled

ProjectCreatedFromQuotation

---

# Listeners

GeneratePDF

SendQuotationEmail

NotifySalesTeam

NotifyClient

BroadcastQuotationStatus

ClearQuotationCache

GenerateAnalytics

---

# Policies

LeadPolicy

QuotationPolicy

SiteVisitPolicy

ApprovalPolicy

---

# Livewire Components

Request Quotation Form

Quotation Wizard

Quotation Summary

Quotation Status

Revision History

Approval Timeline

Site Visit Scheduler

Lead Dashboard

Sales Dashboard

---

# Filament Resources

Sales Lead Resource

Quotation Resource

Quotation Template Resource

Site Visit Resource

Lead Source Resource

Approval Resource

---

# Frontend Pages

Request Quotation

Quotation Success

Track Quotation

Quotation Details (Client Portal)

Sales Contact

---

# Public Quotation Form

Allow visitors to submit:

Full Name

Email

Phone

Project Type

County

Project Location

Budget Range

Estimated Timeline

Required Services

Project Description

Attachments

Preferred Contact Method

---

# Attachment Support

Accept

PDF

DOCX

Images

Blueprints

CAD Drawings (Future)

ZIP Archives

Maximum upload limits configurable.

---

# Quotation Builder

Support

Multiple Sections

Grouped Items

Taxes

Discounts

Optional Items

Custom Notes

Terms & Conditions

Validity Period

Multiple Revisions

---

# Site Visits

Sales staff may schedule

Site Visits

Virtual Meetings

Phone Consultations

Each visit records

Date

Time

Location

Engineer

Notes

Photos

Recommendations

---

# Approval Workflow

Draft

↓

Internal Review

↓

Approved

↓

Sent to Client

↓

Client Reviewing

↓

Revision Requested

↓

Updated

↓

Accepted

↓

Project Created

---

# Client Actions

View quotation

Download PDF

Accept quotation

Reject quotation

Request revision

Send questions

Upload supporting documents

---

# PDF Generation

Generate professional PDFs including

Company branding

Logo

Terms

Scope of work

Pricing

Conditions

QR Code

Revision number

Digital verification code

Queue all PDF generation jobs.

---

# Email Workflow

Automatic emails for

Quotation Created

Quotation Approved

Quotation Sent

Revision Requested

Quotation Accepted

Quotation Expired

Site Visit Scheduled

---

# Homepage Integration

Request Quotation CTA

Featured Services CTA

Contact CTA

Recent Success Stories

---

# Analytics

Track

Lead sources

Conversion rate

Acceptance rate

Average quotation value

Sales pipeline

Time to approval

Time to acceptance

Most requested services

---

# Redis Strategy

Cache

Quotation templates

Pricing configuration

Lead statistics

Dashboard widgets

Sales reports

---

# Queue Strategy

Queue

PDF generation

Email delivery

Analytics

Search indexing

Notifications

Large attachments

---

# Laravel Reverb

Broadcast

New quotation

Approval status

Revision requested

Quotation accepted

Lead assigned

---

# Search

Search by

Quotation Number

Client

Lead

Status

Location

Engineer

Date

Project Type

---

# Security

Validate every upload.

Only authorized staff may edit quotations.

Clients may only access their own quotations.

Every change must be activity logged.

---

# Performance

Cache templates

Queue PDF generation

Optimize queries

Use eager loading

Cache dashboards

---

# Testing

Quotation Builder

Approval Workflow

PDF Generation

Email Delivery

Client Portal

Policies

Caching

Search

Analytics

---

# Documentation

Update

Quotation Guide

Sales Workflow

Architecture

README

API Documentation

---

# Definition of Done

- [ ] Lead management complete
- [ ] Quotation requests operational
- [ ] Site visits operational
- [ ] PDF generation working
- [ ] Email workflow complete
- [ ] Approval workflow complete
- [ ] Client acceptance working
- [ ] Analytics operational
- [ ] Redis integrated
- [ ] Reverb broadcasting operational
- [ ] Queues operational
- [ ] Tests passing
- [ ] Documentation updated

# =============================================================================
# PHASE 10
# CLIENT RELATIONSHIP MANAGEMENT (CRM LITE)
# =============================================================================

# Objective

Develop a centralized Client Relationship Management module that manages every
customer from the moment they become a client through the entire lifecycle of
their relationship with Zytech Contractors.

The Client Domain becomes the single source of truth for all customer
information across the platform.

Every module interacts with Clients instead of storing duplicated customer
information.

---

# Estimated Duration

2 Weeks

---

# Dependencies

- Phase 9 Complete

---

# Deliverables

- Client Profiles
- Company Clients
- Individual Clients
- Contact Persons
- Addresses
- Client Documents
- Communication History
- Notes
- Client Timeline
- Client Status
- Client Dashboard
- Activity History
- Client Analytics
- Favorite Projects
- Saved Quotations
- Portal Access
- Notification Preferences

---

# Database

Tables

clients

client_contacts

client_addresses

client_documents

client_notes

client_timelines

client_preferences

client_status_history

client_tags

client_groups

client_activity_logs

client_favorites

client_notifications

client_settings

---

# Models

Client

ClientContact

ClientAddress

ClientDocument

ClientNote

ClientTimeline

ClientPreference

ClientGroup

ClientTag

---

# Enums

ClientType

ClientStatus

CommunicationMethod

DocumentVisibility

RelationshipStatus

NotificationPreference

---

# DTOs

ClientData

ContactData

AddressData

DocumentData

PreferenceData

---

# Repositories

ClientRepository

ContactRepository

DocumentRepository

TimelineRepository

---

# Services

ClientService

DocumentService

CommunicationService

ClientAnalyticsService

TimelineService

---

# Actions

CreateClient

UpdateClient

ArchiveClient

MergeClients

UploadClientDocument

AssignPortalAccess

RecordCommunication

UpdatePreferences

GenerateClientSummary

---

# Events

ClientCreated

ClientUpdated

ClientArchived

DocumentUploaded

PortalAccessGranted

CommunicationLogged

ClientMerged

---

# Listeners

NotifyAssignedStaff

IndexClient

ClearClientCache

BroadcastClientUpdate

LogClientActivity

---

# Policies

ClientPolicy

ClientDocumentPolicy

ClientNotePolicy

---

# Livewire Components

Client Dashboard

Client Timeline

Documents

Contacts

Addresses

Communication History

Saved Quotations

Favorite Projects

Notification Settings

---

# Filament Resources

Client Resource

Document Resource

Group Resource

Tag Resource

---

# Frontend Pages

Client Dashboard

Profile

Documents

Notifications

Settings

---

# Client Profile

Every client profile should include

Company Logo (optional)

Profile Photo

Contact Details

Multiple Contacts

Addresses

Preferred Contact Method

Industry

Notes

Assigned Sales Representative

Assigned Project Manager

Current Quotations

Completed Projects

Upcoming Meetings

Activity Timeline

---

# Client Timeline

Display chronological events

Lead Created

Quotation Requested

Quotation Approved

Project Started

Project Completed

Document Uploaded

Meeting Scheduled

Support Request

Communication Logged

Portal Login

---

# Client Documents

Store

Contracts

Approved Quotations

Invoices (Future)

Receipts (Future)

Project Drawings

Certificates

Inspection Reports

Warranty Documents

Manuals

Photos

Private Notes

---

# Communication History

Log every interaction

Emails

Phone Calls

WhatsApp (Future)

Meetings

Site Visits

Portal Messages

Internal Notes

---

# Notification Preferences

Clients may configure

Email Notifications

SMS (Future)

Portal Notifications

Marketing Emails

Project Updates

Quotation Updates

Newsletter

---

# Homepage Integration

Client Login

Client Testimonials

Customer Stories

---

# Analytics

Track

Total Clients

Active Clients

Inactive Clients

Client Growth

Client Retention

Most Requested Services

Average Project Value

Average Quotation Value

---

# Redis Strategy

Cache

Client Dashboard

Recent Clients

Client Statistics

Frequently Accessed Profiles

---

# Queue Strategy

Queue

Email Notifications

Document Processing

Analytics

Search Indexing

Report Generation

---

# Laravel Reverb

Broadcast

Client Profile Updated

Document Uploaded

New Notification

Portal Message

Project Update

---

# Search

Search by

Client Name

Company

Email

Phone

Project

Quotation

Assigned Staff

Tags

County

---

# Security

Clients can only access their own records.

Documents must support public/private visibility.

Every action must be logged.

Role-based authorization required for all staff actions.

---

# Performance

Cache dashboards

Optimize document queries

Lazy-load timelines

Index searchable fields

Use eager loading

---

# Testing

CRUD

Policies

Document Uploads

Notifications

Caching

Search

Portal Access

---

# Documentation

Update

Client Module

CRM Guide

Architecture

README

API Documentation

---

# Definition of Done

- [ ] Client profiles operational
- [ ] Timeline working
- [ ] Documents working
- [ ] Communication history complete
- [ ] Dashboard operational
- [ ] Redis integrated
- [ ] Reverb integrated
- [ ] Queues operational
- [ ] Tests passing
- [ ] Documentation updated