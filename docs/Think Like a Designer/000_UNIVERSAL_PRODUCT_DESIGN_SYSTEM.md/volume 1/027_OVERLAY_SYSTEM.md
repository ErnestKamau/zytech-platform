# =============================================================================
# UNIVERSAL PRODUCT DESIGN SYSTEM (UPDS)
# =============================================================================
# 027_OVERLAY_SYSTEM.md
# =============================================================================

Version 1.0

---

# Table of Contents

1. Purpose

2. Overlay Philosophy

3. Overlay Principles

4. Overlay Hierarchy

5. Overlay Types

6. Modal System

7. Drawer System

8. Popover System

9. Tooltip System

10. Dropdown System

11. Context Menu

12. Command Palette

13. Image & Media Viewer

14. Overlay Layering

15. Focus Management

16. Animations

17. Accessibility

18. Performance

19. AI Rules

20. Best Practices

21. Common Mistakes

22. Anti-patterns

23. Implementation

24. Checklist

---

# Purpose

Overlays allow users to complete tasks without leaving their current context.

Instead of navigating to another page,

the interface temporarily creates a new interaction layer.

Users remain oriented.

Context is preserved.

---

# Overlay Philosophy

Navigation changes location.

Overlays extend the current experience.

Choose an overlay when the user should remain mentally connected to the current page.

Avoid unnecessary page transitions.

---

# Overlay Principles

Every overlay should be

Focused

Temporary

Accessible

Predictable

Dismissible

Responsive

Non-destructive

Overlays should simplify workflows,

not interrupt them.

---

# Overlay Hierarchy

Tooltip

↓

Popover

↓

Dropdown

↓

Context Menu

↓

Drawer

↓

Modal

↓

Full Screen Overlay

Higher layers require greater user attention.

Never overuse high-priority overlays.

---

# Overlay Types

Tooltip

Popover

Dropdown

Context Menu

Modal

Drawer

Bottom Sheet

Command Palette

Lightbox

Media Viewer

Picker

Full Screen Overlay

Each serves a distinct purpose.

---

# Modal System

Use modals for

Confirmation

Small Forms

Quick Editing

Critical Decisions

Authentication Prompts

Never place large workflows inside a modal.

If scrolling becomes excessive,

use a dedicated page instead.

---

# Drawer System

Drawers slide into view while preserving page visibility.

Best for

Filters

Settings

Notifications

Shopping Cart

Project Details

Drawers work well when users frequently switch between content and controls.

---

# Popover System

Popovers display contextual information.

Examples

User Card

Quick Actions

Formatting Controls

Calendar Preview

Keep popovers lightweight.

Avoid embedding complex interfaces.

---

# Tooltip System

Tooltips explain existing interface elements.

Never use tooltips to communicate essential information.

Users should understand the interface without hovering.

Tooltips enhance understanding.

They do not replace labels.

---

# Dropdown System

Dropdowns present selectable options.

Use for

Actions

Selections

Sorting

Filtering

Navigation

Avoid very long dropdowns.

Prefer searchable lists for large datasets.

---

# Context Menu

Context menus expose actions relevant to a selected object.

Examples

Rename

Duplicate

Delete

Share

Move

Keep options relevant to the selected item.

Avoid generic menus.

---

# Command Palette

Command palettes prioritize speed.

Support

Navigation

Actions

Search

Recent Items

Shortcuts

Power users should accomplish most tasks without touching the mouse.

---

# Image & Media Viewer

Media overlays should support

Zoom

Fullscreen

Keyboard Navigation

Touch Gestures

Captions

Downloads

Related Media

Media should remain immersive without losing orientation.

---

# Overlay Layering

Define a consistent stacking order.

Tooltips

↓

Dropdowns

↓

Popovers

↓

Drawers

↓

Modals

↓

Lightboxes

↓

Critical Alerts

Never allow overlapping overlays to compete.

One primary interaction layer at a time.

---

# Focus Management

Opening an overlay should

Move focus inside.

Trap focus appropriately.

Restore focus when closed.

Keyboard users should never lose context.

---

# Animations

Overlays should animate naturally.

Recommended motion

Fade

Scale

Slide

Glass Blur Transition

Opacity

Motion should reinforce hierarchy,

not distract.

---

# Accessibility

Every overlay must support

Keyboard navigation

Escape to close

Visible focus

Screen readers

Logical reading order

Reduced motion

Touch accessibility

Accessibility applies to every overlay.

---

# Performance

Overlays should

Open instantly

Lazy-load heavy content

Unload when dismissed

Preserve responsiveness

Avoid unnecessary rendering

Fast overlays feel lightweight.

---

# AI Rules

AI should

Choose the correct overlay type

Avoid unnecessary modals

Prefer drawers for supporting workflows

Use tooltips sparingly

Maintain accessibility

Respect layering

Reuse existing overlay patterns

---

# Best Practices

Preserve context.

Reduce navigation.

Use the smallest effective overlay.

Animate naturally.

Maintain focus.

Support keyboard users.

Keep overlays lightweight.

---

# Common Mistakes

Nested modals.

Huge dropdowns.

Tooltip-only instructions.

Blocking users unnecessarily.

Too many overlays at once.

Overloaded drawers.

Poor mobile adaptation.

---

# Anti-patterns

Modal inside modal.

Drawer inside drawer.

Fullscreen modal for small tasks.

Critical information inside tooltips.

Unclosable overlays.

Unexpected overlay stacking.

Every overlay should reduce complexity.

Never increase it.

---

# Implementation

Applications consume Overlay Patterns.

Overlay Patterns consume Components.

Components consume Recipes.

Recipes consume Tokens.

Applications should never invent inconsistent overlay behaviour.

---

# Overlay Checklist

□ Is the correct overlay type used?

□ Does it preserve context?

□ Is it accessible?

□ Is focus managed correctly?

□ Is motion appropriate?

□ Is performance acceptable?

□ Is it responsive?

□ Is it dismissible?

□ Can AI classify the overlay correctly?

□ Is it reusable?

Only then is the overlay approved.

---

# Final Principle

Overlays should feel like natural extensions of the interface.

They should appear effortlessly,

guide users toward a focused task,

preserve context,

respect accessibility,

and disappear as gracefully as they arrived.

The best overlays are almost invisible—

users simply accomplish their task and continue their journey.