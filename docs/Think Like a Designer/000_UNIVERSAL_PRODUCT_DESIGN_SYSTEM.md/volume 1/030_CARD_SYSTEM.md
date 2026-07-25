# =============================================================================
# UNIVERSAL PRODUCT DESIGN SYSTEM (UPDS)
# =============================================================================
# 030_CARD_SYSTEM.md
# =============================================================================

Version 1.0

---

# Table of Contents

1. Purpose

2. Card Philosophy

3. Card Principles

4. Card Hierarchy

5. Card Anatomy

6. Card Categories

7. Card Variants

8. Card Composition

9. Card Layouts

10. Interactive Cards

11. Media Cards

12. Statistic Cards

13. Project Cards

14. Profile Cards

15. Action Cards

16. Empty Cards

17. Glass Cards

18. Motion

19. Accessibility

20. Performance

21. AI Rules

22. Best Practices

23. Common Mistakes

24. Anti-patterns

25. Implementation

26. Checklist

---

# Purpose

Cards organize related information into meaningful, reusable containers.

A card represents one object,

one idea,

or one action.

Cards should simplify scanning,

comparison,

and interaction.

---

# Card Philosophy

Cards should create structure.

Not decoration.

A card should feel like a self-contained module that can move anywhere in the interface without changing its identity.

Cards are building blocks.

Not page layouts.

---

# Card Principles

Cards should be

Consistent

Scannable

Composable

Accessible

Responsive

Reusable

Purpose-driven

Every card should answer

What is this?

Why does it matter?

What can I do with it?

---

# Card Hierarchy

Card

↓

Header

↓

Media

↓

Body

↓

Metadata

↓

Actions

↓

Footer

Not every card requires every section.

Only include what adds value.

---

# Card Anatomy

Every card may contain

Surface

↓

Header

↓

Title

↓

Subtitle

↓

Media

↓

Content

↓

Supporting Information

↓

Tags

↓

Status

↓

Actions

↓

Footer

↓

Interaction Layer

Each element has a defined role.

---

# Card Categories

Content Card

Project Card

Statistic Card

Profile Card

Feature Card

Pricing Card

Media Card

Dashboard Card

Action Card

Notification Card

Document Card

Timeline Card

Product Card

Location Card

Activity Card

Cards should be categorized by purpose,

not appearance.

---

# Card Variants

Filled

Outlined

Elevated

Flat

Glass

Gradient Accent

Minimal

Interactive

Compact

Expandable

Never invent undocumented variants.

---

# Card Composition

Cards consume

Surface Tokens

Spacing Tokens

Typography Tokens

Color Tokens

Radius Tokens

Elevation Tokens

Motion Tokens

Glass Recipes

Cards never define visual values directly.

---

# Card Layouts

Vertical

Horizontal

Split

Overlay

Media First

Content First

Statistic

Dashboard

Adaptive

Choose layouts based on content,

not preference.

---

# Interactive Cards

Interactive cards should communicate clickability.

Support

Hover

Focus

Pressed

Selected

Expanded

Loading

Disabled

Never make static cards appear interactive.

---

# Media Cards

Media cards display

Images

Videos

Blueprints

Documents

Maps

3D Models

Media should maintain consistent aspect ratios.

Support placeholders until content loads.

Never allow layout shifts.

---

# Statistic Cards

Statistic cards summarize information.

Examples

Revenue

Projects

Pending Samples

Drivers Online

Equipment Status

Include

Primary Value

Trend

Comparison

Supporting Context

Never display numbers without meaning.

---

# Project Cards

Project cards should include

Cover Image

Project Name

Client

Status

Progress

Location

Timeline

Quick Actions

Support media placeholders for future uploads.

---

# Profile Cards

Profile cards should include

Avatar

Name

Role

Status

Contact Information

Quick Actions

Profiles should prioritize recognition.

---

# Action Cards

Action cards encourage interaction.

Examples

Request Quote

Upload Sample

Schedule Inspection

Track Delivery

Book Consultation

Action cards should highlight one clear next step.

---

# Empty Cards

Empty cards educate users.

Explain

Why nothing exists

How to add content

Expected outcome

Never leave blank containers.

---

# Glass Cards

Glass cards inherit the Glass Recipe.

Characteristics

Backdrop Blur

Low-opacity surface

Inset highlight

Soft reflection

Gradient border

Optional spotlight interaction

Optional shimmer sweep

Subtle elevation

Glass cards should feel premium,

not decorative.

Use stronger glass effects in dark themes,

lighter effects in light themes.

---

# Motion

Cards support

Hover Elevation

Soft Scale

Glass Spotlight

Border Glow

Shimmer Sweep

Expand

Collapse

Fade

Motion should communicate interaction,

not entertain.

---

# Accessibility

Cards must support

Keyboard navigation

Visible focus

Screen readers

Logical reading order

Large touch targets

Reduced motion

Interactive cards must behave like buttons or links.

---

# Performance

Cards should

Reuse tokens

Lazy-load media

Use skeleton placeholders

Virtualize large collections

Optimize rendering

Avoid expensive blur effects on large grids.

---

# AI Rules

AI should

Reuse documented card recipes

Choose the correct card category

Maintain hierarchy

Respect spacing tokens

Use media placeholders

Preserve accessibility

Never invent random card layouts

---

# Best Practices

Design cards around content.

Keep hierarchy obvious.

Support responsive layouts.

Use skeleton loading.

Maintain consistent spacing.

Encourage one primary action.

Use glass intentionally.

---

# Common Mistakes

Too much information.

Multiple competing actions.

Random card heights.

Heavy shadows.

Decorative gradients.

Inconsistent spacing.

Poor typography hierarchy.

---

# Anti-patterns

Cards inside cards inside cards.

Clickable cards without hover states.

Glass on every card.

Uneven grid layouts.

Image stretching.

Decorative icons everywhere.

Meaningless statistics.

Cards should organize.

Never overwhelm.

---

# Implementation

Applications consume Card Recipes.

Cards consume

Surface Tokens

Spacing Tokens

Typography Tokens

Color Tokens

Radius Tokens

Motion Tokens

Glass Recipes

Elevation Tokens

Applications should never create undocumented card styles.

---

# Card Checklist

□ Is the correct card category used?

□ Is hierarchy clear?

□ Does it consume design tokens?

□ Is spacing consistent?

□ Is media optimized?

□ Are skeletons available?

□ Is accessibility supported?

□ Is motion subtle?

□ Can AI classify the card correctly?

□ Is it reusable?

Only then is the card approved.

---

# Final Principle

Cards are the modular language of modern interfaces.

Great cards organize information,

focus attention,

encourage interaction,

adapt across every screen,

and create products that feel cohesive,

predictable,

and beautifully structured.