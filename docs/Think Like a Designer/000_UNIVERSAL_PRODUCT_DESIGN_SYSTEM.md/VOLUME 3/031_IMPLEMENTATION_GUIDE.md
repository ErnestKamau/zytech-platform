# =============================================================================
# UNIVERSAL PRODUCT DESIGN SYSTEM (UPDS)
# =============================================================================
# 031_IMPLEMENTATION_GUIDE.md
# =============================================================================

Version 1.0

---

# Purpose

The Universal Product Design System defines how products should look.

This document defines how products should be built.

Its purpose is to translate design decisions into maintainable, scalable implementations across different platforms.

Supported platforms include

• Laravel
• Livewire
• Alpine.js
• Bootstrap
• CSS
• JavaScript
• React
• React Native

Every implementation should consume the same design language.

---

# Philosophy

Design should be implemented once.

Reused everywhere.

Applications should compose interfaces from approved tokens, recipes, components and patterns.

Never recreate components.

Never duplicate styling.

Never hardcode visual values.

---

# Design Architecture

Every interface follows the same pipeline.

Design Tokens

↓

Recipes

↓

Components

↓

Patterns

↓

Layouts

↓

Pages

↓

Products

Pages consume layouts.

Layouts consume patterns.

Patterns consume components.

Components consume recipes.

Recipes consume tokens.

Only tokens contain raw visual values.

---

# Recommended Project Structure

resources/

    css/

        tokens/

        recipes/

        components/

        layouts/

        pages/

        utilities/

    js/

    views/

    theme/

Every directory has a single responsibility.

---

# CSS Architecture

Load CSS in the following order.

Reset

↓

Fonts

↓

Tokens

↓

Recipes

↓

Utilities

↓

Components

↓

Layouts

↓

Pages

↓

Print

↓

Overrides

Never reverse this order.

---

# Tokens

Tokens define primitive values.

Examples

Colors

Spacing

Typography

Radius

Elevation

Opacity

Glass

Motion

Shadows

Borders

Breakpoints

Z-index

Timing

No component should define these values directly.

---

# Recipes

Recipes combine tokens into reusable design decisions.

Examples

Primary Button

Glass Card

Project Card

Sidebar

Navbar

Dashboard Widget

Modal

Toast

Search Bar

Recipe changes should update every consuming component.

---

# Components

Components implement recipes.

Examples

Button

Input

Select

Card

Table

Badge

Avatar

Tabs

Accordion

Pagination

Tooltip

Modal

Drawer

Every component should be reusable.

---

# Patterns

Patterns combine multiple components into common workflows.

Examples

Authentication

Dashboard

Project Listing

Project Details

Multi-Step Wizard

Search + Filters

Data Table

Analytics

Checkout

Media Gallery

Patterns solve recurring user problems.

---

# Layouts

Layouts organize patterns.

Examples

Public Website

Portal

Dashboard

Authentication

Landing Page

Documentation

Settings

Reports

A layout defines structure.

Not content.

---

# Pages

Pages assemble layouts and patterns.

Pages should contain minimal styling.

Their responsibility is composition.

Never design directly inside a page.

---

# Laravel Guidelines

Blade templates should compose layouts.

Keep business logic out of Blade.

Prefer reusable Blade or Livewire components over duplicated markup.

Use named routes consistently.

Keep page templates focused on composition.

---

# Livewire Guidelines

Each Livewire component should have one responsibility.

Examples

Project Table

Project Form

Notification Center

Media Gallery

Search Panel

Avoid large "god components".

Compose smaller components together.

---

# Alpine.js Guidelines

Alpine.js manages interaction.

Examples

Dropdowns

Tabs

Accordions

Tooltips

Simple Modals

Expandable Sections

Avoid placing business logic in Alpine.

Use it to enhance the interface, not replace backend behavior.

---

# Bootstrap Guidelines

Bootstrap provides the layout foundation.

Use

Grid

Containers

Responsive Utilities

Flex

Spacing Utilities (only where appropriate)

Do not override Bootstrap randomly.

Extend it through your design tokens and recipes.

---

# React & React Native Guidelines

Use the same

Colors

Spacing

Typography

Radius

Motion

Component names

Recipe names

The renderer changes.

The design language does not.

---

# Images & Media

Applications should support placeholders for

Hero Images

Project Galleries

Construction Photography

Blueprints

Videos

Maps

Documents

Company Logos

Avatars

3D Renders

Before & After Comparisons

Reserve space before assets load to prevent layout shift.

---

# Skeleton Loading

Prefer skeletons over spinners.

Every major component should have a matching skeleton.

Examples

Cards

Tables

Forms

Charts

Media

Dashboards

Timelines

Skeletons should closely resemble the final layout.

---

# Motion

Animations should consume Motion Tokens.

Examples

Hover

Focus

Expand

Collapse

Page Transition

Drawer

Modal

Spotlight

Shimmer Sweep

Respect reduced-motion preferences.

---

# Theme Support

Every implementation must support

Light Theme

Dark Theme

Glass Theme

High Contrast Theme

Brand Themes

No component should depend on a single theme.

---

# Accessibility

Every implementation must support

Keyboard Navigation

Visible Focus

Screen Readers

Logical Heading Structure

Touch Targets

Color Contrast

Reduced Motion

Accessibility is a default requirement.

---

# Performance

Optimize

Images

Video

Fonts

Animations

Blur Effects

Rendering

Lists

Tables

Maps

Use lazy loading where appropriate.

Avoid unnecessary DOM complexity.

---

# Naming Conventions

Use clear, predictable names.

Examples

button-primary

card-project

table-samples

layout-dashboard

pattern-auth

recipe-glass-card

token-color-brand

Consistency is more important than brevity.

---

# AI Implementation Rules

AI should

Search for existing recipes before creating new ones.

Reuse components whenever possible.

Compose rather than duplicate.

Recommend additions to Theme Studio instead of inventing new designs.

Follow the architecture pipeline.

---

# Developer Workflow

When building a new feature

1. Identify the user goal.

2. Check Theme Studio for existing recipes.

3. Assemble approved components.

4. Build the pattern.

5. Place it inside the correct layout.

6. Validate accessibility.

7. Validate responsiveness.

8. Validate performance.

9. Test all states.

10. Ship.

Never begin with custom CSS.

---

# Final Principle

Every product should feel as though it was built by the same team.

The technology stack may change.

The framework may change.

The platform may change.

The design language must not.

Implementation is not the process of creating interfaces.

It is the process of faithfully translating the Universal Product Design System into working software.