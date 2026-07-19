# Zytech Contractors Platform

# 07_UI_UX.md

> Version: 1.0
> Status: Living Document
> Project: Zytech Contractors Digital Platform
> Last Updated: July 2026

---

# Table of Contents

1. Philosophy
2. Design Principles
3. Brand Personality
4. Visual Language
5. Color System
6. Typography
7. Spacing System
8. Layout System
9. Grid System
10. Iconography
11. Elevation & Shadows
12. Border Radius
13. Motion Design
14. Component Library
15. Forms
16. Tables
17. Cards
18. Navigation
19. Responsive Design
20. Accessibility
21. Media Guidelines
22. Empty States
23. Error States
24. Loading States
25. Micro Interactions
26. Design Tokens
27. Performance Guidelines

---

# Philosophy

Every interface should communicate the same qualities as a well-constructed building.

- Strong
- Clean
- Precise
- Organized
- Professional
- Timeless

Users should feel that Zytech pays attention to detail.

The software should reflect the company's construction quality.

---

# Design Principles

Every screen should prioritize:

✓ Clarity

✓ Simplicity

✓ Confidence

✓ Performance

✓ Accessibility

✓ Consistency

✓ Scalability

---

# Brand Personality

The platform should feel:

Modern

Professional

Premium

Architectural

Minimal

Reliable

Elegant

Not flashy.

Not playful.

Not corporate grey.

Think:

"A premium architectural studio."

---

# Emotional Goals

Visitors should feel:

• Trust

• Confidence

• Curiosity

• Inspiration

• Professionalism

Clients should feel:

• Control

• Transparency

• Progress

Staff should feel:

• Productivity

• Efficiency

• Simplicity

---

# Visual Language

The visual language should be inspired by:

- Modern architecture
- Concrete
- Glass
- Steel
- Natural lighting
- Technical drawings
- Clean geometry

The UI should use generous white space.

Content should breathe.

Avoid clutter.

---

# Color Philosophy

The color palette should communicate professionalism rather than decoration.

Colors should support content—not compete with it.

---

# Primary Color

Construction Blue

Used for:

- Primary buttons
- Links
- Active states
- Focus indicators

---

# Secondary Color

Architectural Slate

Used for:

- Headers
- Icons
- Navigation
- Text emphasis

---

# Accent Color

Construction Gold

Used sparingly for:

- Highlights
- Featured projects
- Premium badges
- Statistics

---

# Neutral Palette

Background

Surface

Border

Muted Text

Primary Text

Secondary Text

Success

Warning

Danger

Info

Every color should have:

50

100

200

300

400

500

600

700

800

900

---

# Dark Mode

Not included in Version 1.

However,

Every component should avoid assumptions that prevent future dark mode support.

---

# Typography

Primary Font

Geist

Fallback

System UI

Headings

Bold

Large

Well spaced

Body

Readable

16px minimum

Line height:

1.6+

Never use justified text.

---

# Heading Hierarchy

H1

One per page

H2

Major sections

H3

Subsections

H4+

Only when necessary.

---

# Reading Width

Maximum

75 characters

Long paragraphs reduce readability.

---

# Spacing System

Use an 8-point spacing system.

```
4

8

12

16

24

32

40

48

64

80

96
```

No arbitrary spacing.

---

# Grid System

Desktop

12 Columns

Tablet

8 Columns

Mobile

4 Columns

Layouts should remain visually balanced.

---

# Containers

Small

Medium

Large

Extra Large

Full Width

Content should never stretch excessively.

---

# Border Radius

Consistent across the platform.

Example

Buttons

Cards

Inputs

Modals

Dropdowns

Should all use the same radius scale.

---

# Shadows

Minimal.

Use elevation only where necessary.

Avoid excessive floating elements.

---

# Iconography

Use Heroicons.

Icons should always communicate meaning.

Avoid decorative icons.

Every icon must improve usability.

---

# Photography

Photography should emphasize:

Real Projects

Construction Teams

Architectural Details

Materials

Equipment

Natural Lighting

Avoid stock imagery whenever possible.

---

# Video Guidelines

Videos should:

Autoplay silently

Loop

Lazy load

Remain compressed

Never delay page rendering.

---

# Cards

Cards should be used consistently.

Supported card types

Project Card

Service Card

Article Card

Testimonial Card

Statistic Card

Profile Card

Quote Card

Every card follows the same spacing rules.

---

# Buttons

Primary

Secondary

Outline

Ghost

Danger

Loading

Disabled

Every button should include:

Hover

Focus

Pressed

Disabled

Loading

States.

---

# Forms

Forms should:

Validate inline.

Provide helpful messages.

Never clear entered data after validation errors.

Support keyboard navigation.

Autocomplete wherever possible.

---

# Inputs

Supported inputs

Text

Textarea

Email

Phone

Select

Combobox

Autocomplete

Checkbox

Toggle

Radio

File Upload

Image Upload

Date Picker

Rich Editor

Search

Every input should have:

Label

Helper text

Validation

Error message

Success state

---

# Tables

Desktop:

Rich data tables.

Mobile:

Cards instead of tables where appropriate.

Support:

Sorting

Searching

Filtering

Pagination

Column visibility

Exporting

---

# Navigation

Primary Navigation

Desktop

Horizontal

Sticky

Transparent over hero

Solid after scrolling.

Mobile

Slide-over menu

Touch friendly

Keyboard accessible.

---

# Breadcrumbs

Required for:

Projects

Services

Knowledge Articles

Client Portal

Admin

---

# Animations

Animations should be subtle.

Examples

Fade

Slide

Scale

Counter

Reveal

Timeline

Avoid flashy effects.

Animation duration:

150–300ms

---

# Scroll Animations

Allowed

Fade In

Reveal

Stagger

Counter

Timeline

Parallax (minimal)

Respect

prefers-reduced-motion.

---

# Loading States

Use:

Skeleton Loaders

Progress Bars

Button Loading

Lazy Components

Avoid spinners where possible.

---

# Empty States

Every empty state should include:

Illustration

Message

Suggested Action

CTA

Example

"No Projects Yet"

↓

Upload First Project

---

# Error States

Friendly

Actionable

Clear

Never expose technical errors.

---

# Notifications

Success

Warning

Info

Danger

Toast notifications should disappear automatically.

Critical notifications require dismissal.

---

# Accessibility

Target

WCAG 2.2 AA

Requirements

Keyboard navigation

Screen readers

Contrast ratio

Focus states

Alt text

Reduced motion

Semantic HTML

Accessible forms

---

# Responsive Strategy

Mobile First

Breakpoints

Small

Medium

Large

XL

2XL

Components should scale—not merely shrink.

---

# Media Guidelines

Images

AVIF

WebP

Responsive

Lazy Loaded

Videos

Optimized

Poster images

Adaptive quality

Documents

Preview where possible.

---

# Design Tokens

Every visual value should come from tokens.

Never hardcode:

Colors

Spacing

Radius

Typography

Transitions

Shadows

---

# Performance

UI decisions must support performance.

Avoid:

Heavy animations

Large images

Layout shifts

Blocking JavaScript

Excessive DOM depth

Target:

95+ Lighthouse

100 Accessibility

100 Best Practices

100 SEO

---

# Guiding Principle

Beautiful software is not software with the most animations.

Beautiful software is software that feels effortless to use.

Every interaction should reduce friction, increase confidence, and reinforce Zytech's reputation for precision and quality.

---
---

# Appendix A — Component Inventory

## Purpose

The Zytech Contractors Platform follows a component-driven design system.

Every visual element should be implemented as a reusable component rather than duplicated throughout the application.

The objective is to achieve:

- Consistency
- Maintainability
- Accessibility
- Testability
- Reusability
- Faster Development

Whenever a UI pattern is used more than once, it should become a reusable component.

---

# Component Categories

The design system is divided into the following categories:

```
Foundation
│
├── Typography
├── Colors
├── Icons
├── Spacing
├── Layout
│
Forms
│
├── Inputs
├── Buttons
├── Validation
├── Uploads
│
Navigation
│
├── Navbar
├── Breadcrumbs
├── Sidebar
├── Pagination
│
Content
│
├── Cards
├── Badges
├── Statistics
├── Timelines
│
Feedback
│
├── Alerts
├── Toasts
├── Loading
├── Empty States
│
Media
│
├── Gallery
├── Video
├── Before / After
├── Map
```

---

# Foundation Components

## Typography

Components

- Heading
- Subheading
- Paragraph
- Caption
- Lead Text
- Code
- Quote

Requirements

- Semantic HTML
- Responsive sizing
- Accessible contrast
- Design token driven

---

## Icons

Library

Heroicons

Rules

- Decorative icons must be hidden from screen readers.
- Informational icons require accessible labels.
- Icons should never replace meaningful text.

---

## Dividers

Support

- Horizontal
- Vertical
- Text Divider
- Decorative Divider

---

# Navigation Components

## Navbar

Features

- Sticky
- Transparent over Hero
- Solid on scroll
- Mobile Drawer
- Active state
- Dropdown support

Future

Mega Menu support.

---

## Breadcrumbs

Supported Pages

- Projects
- Services
- Articles
- Client Portal
- Admin

Rules

- Automatically generated.
- Structured data enabled.
- Responsive.

---

## Sidebar

Used in

- Client Portal
- Filament
- Future ERP

Features

- Collapse
- Groups
- Active indicators
- Permission aware

---

## Pagination

Support

- Numbered
- Previous/Next
- Infinite Scroll (future)

---

# Form Components

## Buttons

Supported Variants

Primary

Secondary

Outline

Ghost

Success

Danger

Warning

Icon

Loading

Disabled

Sizes

XS

SM

MD

LG

XL

States

Hover

Focus

Pressed

Loading

Disabled

---

## Inputs

Supported Types

- Text
- Email
- Password
- Number
- Phone
- URL
- Search
- Textarea
- Date
- Time
- DateTime
- Color
- Rich Text
- Markdown
- File Upload
- Image Upload

Shared Behaviour

- Label
- Placeholder
- Helper Text
- Validation
- Error Message
- Success State

---

## Select Components

Support

- Single Select
- Multi Select
- Searchable
- Async
- Grouped

---

## Upload Components

Support

- Images
- Videos
- Documents

Future

- Drag & Drop
- Folder Uploads
- Chunk Uploads

---

# Content Components

## Project Card

Displays

- Hero Image
- Project Name
- County
- Category
- Services
- Completion Year

Actions

View Project

---

## Service Card

Displays

- Icon
- Image
- Description
- CTA

---

## Testimonial Card

Displays

- Client
- Quote
- Rating
- Related Project

---

## Statistic Card

Displays

- Number
- Icon
- Label

Supports

Animated Counters

---

## Timeline

Displays

Construction progress.

Supports

- Vertical
- Horizontal
- Milestones
- Images
- Documents

---

# Media Components

## Gallery

Features

- Grid
- Masonry
- Fullscreen
- Lightbox
- Keyboard Navigation

---

## Video Player

Supports

- MP4
- YouTube
- Vimeo

Features

- Poster Image
- Captions
- Lazy Loading

---

## Before / After

Supports

- Horizontal Slider
- Touch
- Keyboard
- Fullscreen

---

## Interactive Kenya Map

Features

- County Selection
- Project Pins
- Filters
- Clustered Markers

Future

Heatmaps.

---

# Feedback Components

Supported

- Alert
- Toast
- Banner
- Inline Validation
- Success
- Error
- Warning
- Information

---

# Loading Components

Supported

- Skeleton Cards
- Skeleton Table
- Skeleton Gallery
- Button Loading
- Progress Bar

Avoid generic spinners whenever possible.

---

# Empty States

Every empty state should contain:

- Illustration
- Heading
- Explanation
- Primary CTA
- Optional Secondary CTA

---

# Reusability Rules

Every reusable component should:

✓ Support dark mode in future.

✓ Be configurable.

✓ Be documented.

✓ Be testable.

✓ Be accessible.

✓ Avoid duplicated styling.

✓ Use design tokens.

✓ Support Livewire updates.

✓ Support Alpine interactions.

✓ Be UUID agnostic.

---

# Component Completion Checklist

Before introducing a new component verify:

- Can an existing component solve this?
- Is it reusable?
- Is it responsive?
- Is it accessible?
- Is it documented?
- Is it tested?
- Does it follow design tokens?


---
---

# Appendix B — Motion Library

## Purpose

Motion exists to communicate state, hierarchy, continuity, and feedback.

Animations should never exist purely for decoration.

Every animation must improve usability.

---

# Motion Principles

Motion should be:

- Intentional
- Fast
- Predictable
- Consistent
- Performant
- Accessible

Avoid:

- Flashy effects
- Long transitions
- Bounce animations
- Excessive parallax

---

# Timing Scale

| Speed | Duration | Usage |
|--------|----------|------|
| Instant | 0ms | Immediate feedback |
| Fast | 150ms | Hover states |
| Normal | 200ms | Cards |
| Medium | 250ms | Modals |
| Slow | 300ms | Page transitions |

Never exceed **400ms**.

---

# Approved Motion Patterns

## Fade

Purpose

Reveal content.

Used For

- Cards
- Sections
- Images

---

## Slide Up

Purpose

Content enters viewport.

Used For

- Project Cards
- Articles
- Testimonials

---

## Scale

Purpose

Interactive feedback.

Used For

- Buttons
- Cards
- Icons

Scale

```
1 → 1.02
```

Never larger.

---

## Counter Animation

Purpose

Animate company statistics.

Used For

- Homepage
- About
- Dashboard

Trigger

Intersection Observer.

Only once.

---

## Timeline Reveal

Purpose

Show project milestones.

Animation

Sequential reveal.

---

## Before / After Slider

Purpose

Compare project transformations.

Animation

Smooth drag only.

No automatic movement.

---

## Toast Animation

Sequence

Fade

↓

Slide

↓

Pause

↓

Fade Out

---

## Modal Animation

Fade Overlay

↓

Scale Modal

↓

Focus First Input

---

## Drawer Animation

Slide

↓

Overlay

↓

Focus Trap

---

## Skeleton Loading

Required for:

- Projects
- Services
- Articles
- Dashboard

Never use page-wide spinners.

---

# Scroll Behaviour

Allow

- Fade
- Reveal
- Counters
- Timeline

Avoid

- Rotations
- Flips
- Excessive scaling

---

# Reduced Motion

Support

```
prefers-reduced-motion
```

Rules

- Disable transitions.
- Disable parallax.
- Disable counters.
- Maintain functionality.

---

# Performance Rules

All animations must:

- Run at 60 FPS.
- Use GPU transforms.
- Avoid layout thrashing.
- Prefer opacity and transform.
- Avoid animating width or height.

---

# Animation Checklist

Before introducing animation ask:

- Does it communicate something?
- Is it accessible?
- Does it improve usability?
- Is it performant?
- Is it consistent?

---

---

# Appendix C — UX Heuristics & Quality Checklist

## Purpose

Every page, feature, and component should satisfy the following usability standards before it is considered complete.

This checklist applies equally to:

- Public Website
- Client Portal
- Admin Panel
- Future ERP Modules

---

# First Impression

Within five seconds, a user should understand:

- Where they are.
- What the page does.
- What action they should take next.

---

# Navigation

Users should always know:

- Current location.
- Previous location.
- Available next actions.

Navigation should never feel confusing.

---

# Call to Action

Every page must contain one primary action.

Examples

- Request Quote
- View Project
- Save Changes
- Publish Article

Avoid competing primary actions.

---

# Readability

Verify

✓ Clear headings

✓ Short paragraphs

✓ Logical spacing

✓ Visual hierarchy

✓ Consistent typography

---

# Feedback

Every interaction must provide immediate feedback.

Examples

- Button loading state.
- Validation message.
- Toast notification.
- Progress indicator.

Never leave users wondering if an action succeeded.

---

# Forms

Every form should:

- Explain required fields.
- Validate inline.
- Preserve entered data.
- Highlight errors clearly.
- Support keyboard navigation.
- Offer autofill where appropriate.

---

# Performance

Every page should:

- Feel responsive.
- Avoid unnecessary loading.
- Lazy-load media.
- Minimize layout shifts.
- Load progressively.

---

# Accessibility

Verify

✓ Keyboard navigation

✓ Focus indicators

✓ Screen reader support

✓ Semantic HTML

✓ Color contrast

✓ Reduced motion

✓ Alt text

---

# Mobile Experience

Every feature should work comfortably on:

- 360px
- 390px
- 430px

Touch targets

Minimum

```
44 × 44 px
```

---

# Error Handling

Errors should:

- Explain the problem.
- Explain how to fix it.
- Avoid technical jargon.
- Preserve user input.

---

# Empty States

Every empty page should answer:

- Why is this empty?
- What should I do next?

Always include a meaningful CTA.

---

# Trust Indicators

Every important page should reinforce trust using:

- Real project imagery.
- Certifications.
- Testimonials.
- Company statistics.
- Professional language.

---

# Consistency

Check that:

- Buttons behave consistently.
- Colors have consistent meaning.
- Icons remain recognizable.
- Animations follow the motion library.
- Components reuse existing patterns.

---

# Engineering Checklist

Before marking a feature complete:

## Functional

- Meets business requirements.
- Acceptance criteria satisfied.

---

## Technical

- Clean architecture.
- Tested.
- Logged where necessary.
- Cached appropriately.
- Queue-aware where applicable.

---

## Design

- Uses approved components.
- Uses design tokens.
- Responsive.
- Accessible.

---

## Content

- Grammar checked.
- SEO reviewed.
- Metadata present.
- Images optimized.

---

## Release Checklist

A feature is ready for production only if it satisfies:

- Business Requirements ✅
- UI/UX Standards ✅
- Accessibility Standards ✅
- Performance Targets ✅
- Security Review ✅
- Automated Tests ✅
- Documentation Updated ✅
- Code Review Approved ✅

---

# Final Design Principle

Every screen should answer one simple question:

> **"Does this interface reflect the same precision, quality, and professionalism that Zytech Contractors delivers in its construction projects?"**

If the answer is **no**, the design is not finished.

---

# Appendix D — Frontend Architecture & Component Standards

## Purpose

This appendix defines the frontend engineering standards for the Zytech Contractors Platform.

Its purpose is to ensure every interface is built consistently, remains maintainable as the platform grows, and fully leverages the capabilities of Laravel, Livewire, Alpine.js, Blade, Tailwind CSS, Filament, Redis, and Laravel Reverb.

These standards apply to:

- Public Website
- Client Portal
- Filament Admin
- Future ERP Modules

The frontend architecture should be:

- Component Driven
- Reactive
- Event Driven
- Accessible
- Performance Focused
- Highly Reusable

---

# Frontend Philosophy

Frontend code should solve business problems—not simply render HTML.

Every component should have one clearly defined responsibility.

Business logic belongs in Livewire.

Presentation belongs in Blade.

Small UI interactions belong in Alpine.

Styling belongs in Tailwind.

Administration belongs in Filament.

No layer should take responsibility for another.

---

# Layer Responsibilities

```
Presentation Layer

↓

Blade

↓

Livewire

↓

Actions

↓

Domain

↓

Infrastructure
```

Each layer should remain independent.

---

# Blade Responsibilities

Blade should be responsible for:

- Layout composition
- Rendering components
- Slots
- Sections
- Semantic HTML
- Accessibility
- Partial composition

Blade should NOT contain:

- Business logic
- Database queries
- Authorization logic
- Complex conditions

Keep Blade declarative.

---

# Livewire Responsibilities

Livewire owns:

- UI state
- User interactions
- Validation
- Form handling
- Pagination
- Searching
- Filtering
- Sorting
- Lazy loading
- Infinite scrolling
- Browser events

Livewire should never become a "God Component."

---

# Alpine.js Responsibilities

Use Alpine only for lightweight client-side interactions.

Examples:

- Dropdowns
- Accordions
- Tabs
- Mobile navigation
- Modals
- Slideover panels
- Image gallery controls
- Before/After slider
- Copy to clipboard
- Theme preparation
- Tooltips

Avoid using Alpine for business logic.

If server state is involved,

use Livewire.

---

# Component Hierarchy

```
Layout

↓

Page

↓

Section

↓

Card

↓

Widget

↓

Primitive Component
```

Example

Homepage

↓

Hero Section

↓

Statistics Section

↓

Statistic Card

↓

Counter

Every level should be reusable.

---

# Blade Directory Structure

resources/views/

```
layouts/

components/

pages/

sections/

partials/

navigation/

forms/

tables/

cards/

media/

modals/

emails/

errors/

vendor/
```

Keep folders shallow.

Avoid deeply nested directories.

---

# Partial Strategy

Partials should be used whenever markup is shared.

Examples

```
hero.blade.php

footer.blade.php

navbar.blade.php

breadcrumbs.blade.php

cta.blade.php

section-header.blade.php

statistics.blade.php
```

Rules

- Stateless
- Small
- Reusable
- Well named

---

# Livewire Directory Structure

```
App

└── Livewire

    Public

    Company

    Services

    Projects

    Articles

    Quotes

    Contact

    Client

    Dashboard

    Shared
```

Each business domain owns its components.

Avoid generic folders.

---

# Livewire Component Standards

Every component should have:

- Single responsibility
- Clear public API
- Typed properties
- Computed properties where appropriate
- Authorization
- Validation
- Loading states
- Empty states
- Error handling

Avoid components exceeding approximately 300–400 lines of PHP where practical. If complexity grows, extract Actions, Form Objects, Services, or child components instead of accumulating logic.

---

# Component Communication

Preferred

```
Event

↓

Listener

↓

Refresh
```

Avoid

```
Parent

↓

Child

↓

Parent

↓

Child

↓

Parent
```

Excessive coupling should be avoided.

---

# Layout Composition

Public

```
App Layout

↓

Navigation

↓

Page

↓

Footer
```

Client Portal

```
Portal Layout

↓

Sidebar

↓

Content

↓

Notifications
```

Filament

Uses Filament Layouts.

Maintain consistent branding.

---

# Design Tokens

Never hardcode

- Colors
- Radius
- Typography
- Shadows
- Spacing
- Durations

Everything should reference centralized design tokens.

---

# Responsive Strategy

Build mobile first.

Supported widths

360

390

430

768

1024

1280

1536

Every component must remain usable on all supported sizes.

---

# State Management

Server State

↓

Livewire

Client UI State

↓

Alpine

Persistent State

↓

Database

Cache

↓

Redis

Do not duplicate state unnecessarily.

---

# Asset Strategy

Images

AVIF

↓

WebP

↓

Fallback

Videos

MP4

Poster Image

Adaptive Compression

Icons

Heroicons

Logos

SVG

---

# Performance Standards

Images

Lazy Loaded

Responsive

Compressed

Videos

Lazy Loaded

Deferred

Components

Lazy Loaded

On Demand

Below-the-fold sections should use lazy loading where appropriate.

---

# Live Updates

Laravel Reverb should power real-time experiences such as:

- Quote status updates
- Client notifications
- Dashboard widgets
- Activity feeds
- Administrative alerts

Only broadcast meaningful events.

---

# Redis Strategy

Use Redis to cache:

- Homepage sections
- Featured projects
- Company statistics
- Services
- Navigation
- Settings
- Frequently accessed reference data

Caches should be invalidated by domain events.

Never rely on time-based cache expiration alone.

---

# Accessibility Standards

Every component should support:

✓ Keyboard navigation

✓ Screen readers

✓ Focus states

✓ Reduced motion

✓ Semantic HTML

✓ Proper labels

Accessibility is a release requirement.

---

# Error Handling

Components should gracefully handle:

- Network failures
- Empty datasets
- Missing media
- Authorization failures
- Validation errors

Never expose technical exception messages.

---

# Naming Standards

Blade

```
project-card.blade.php

service-card.blade.php

statistics-grid.blade.php
```

Livewire

```
ProjectGallery

FeaturedProjects

QuoteForm

KnowledgeSearch
```

Consistency is mandatory.

---

# Reusability Checklist

Before creating a new component ask:

- Does one already exist?
- Can an existing component be extended?
- Should this become a shared component?
- Will another feature need this later?
- Can this be configured instead of duplicated?

Default to reuse over duplication.

---

# AI Development Rules

When generating frontend code, AI agents should:

- Prefer existing components.
- Use Blade partials for repeated markup.
- Keep Livewire components focused.
- Use Alpine only for lightweight interactions.
- Preserve accessibility.
- Follow the design token system.
- Maintain responsive behaviour.
- Use semantic HTML.
- Avoid inline styles.
- Avoid duplicated markup.
- Respect feature boundaries.
- Favor composition over inheritance.

---

# Definition of Frontend Excellence

A frontend implementation is complete only when it is:

- Responsive
- Accessible
- Component-driven
- Reusable
- Reactive
- Performant
- Consistent
- Fully tested
- Documented
- Aligned with the design system

The frontend should communicate the same precision, quality, and attention to detail that Zytech Contractors delivers in every construction project.