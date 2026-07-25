# =============================================================================
# UNIVERSAL PRODUCT DESIGN SYSTEM (UPDS)
# =============================================================================
# 025_DATA_DISPLAY_SYSTEM.md
# =============================================================================

Version 1.0

---

# Table of Contents

1. Purpose

2. Data Display Philosophy

3. Core Principles

4. Information Hierarchy

5. Data Density

6. Display Patterns

7. Card Displays

8. List Displays

9. Table Displays

10. Grid Displays

11. Dashboard Displays

12. Statistics

13. Status Indicators

14. Empty States

15. Loading States

16. Skeleton States

17. Error States

18. Filtering

19. Sorting

20. Searching

21. Pagination

22. Progressive Disclosure

23. Responsive Behaviour

24. Accessibility

25. Performance

26. AI Rules

27. Best Practices

28. Common Mistakes

29. Anti-patterns

30. Implementation

31. Checklist

---

# Purpose

Data exists to help users make decisions.

Displaying data is not enough.

Applications should transform data into understanding.

Every screen should answer

What happened?

What is happening?

What should I do next?

---

# Data Display Philosophy

Users do not want data.

Users want answers.

Applications should organize information to reduce thinking.

Show only what users need.

Reveal additional detail progressively.

---

# Core Principles

Data should be

Clear

Scannable

Consistent

Accurate

Accessible

Responsive

Actionable

Every displayed item should support decision-making.

---

# Information Hierarchy

Users should immediately identify

Primary Information

Secondary Information

Supporting Metadata

Available Actions

Related Information

Visual hierarchy should reflect importance.

---

# Data Density

Choose density based on context.

Comfortable

Reading-focused interfaces.

Balanced

General applications.

Compact

Power users.

Dense layouts should never reduce readability.

---

# Display Patterns

Applications should use appropriate patterns.

Cards

Lists

Tables

Grids

Statistics

Timelines

Calendars

Maps

Charts

Trees

Kanban

Choose patterns based on user goals.

---

# Card Displays

Cards present independent pieces of information.

Best for

Projects

Products

Properties

Laboratory Samples

Employees

Media

Cards prioritize recognition.

Not comparison.

---

# List Displays

Lists prioritize sequential reading.

Suitable for

Notifications

Messages

Tasks

Activities

History

Lists should remain simple.

---

# Table Displays

Tables support comparison.

Suitable for

Invoices

Users

Reports

Inventory

Results

Transactions

Tables prioritize efficiency.

---

# Grid Displays

Grids emphasize visual relationships.

Suitable for

Projects

Gallery

Products

Construction Portfolio

Documents

Media

Grids prioritize discovery.

---

# Dashboard Displays

Dashboards summarize information.

Include

Statistics

Recent Activity

Alerts

Charts

Tasks

Shortcuts

Dashboards answer

What requires attention?

---

# Statistics

Statistics should communicate meaning.

Examples

Revenue

Projects

Samples

Deliveries

Growth

Completion

Never display numbers without context.

---

# Status Indicators

Every status should communicate meaning instantly.

Examples

Pending

Draft

In Progress

Completed

Rejected

Cancelled

Archived

Status should use

Color

Label

Icon

Never rely on color alone.

---

# Empty States

Empty states should educate users.

Explain

Why nothing is shown

How to add content

What happens next

Empty states should encourage action.

---

# Loading States

Loading should preserve layout stability.

Avoid blank pages.

Provide immediate feedback.

Loading should reassure users.

---

# Skeleton States

Prefer skeletons over spinners.

Skeletons preserve

Layout

Spacing

Visual rhythm

User orientation

Skeletons reduce perceived waiting time.

---

# Error States

Errors should explain

What happened

Why

How to recover

Never expose technical errors.

Recovery should always be possible.

---

# Filtering

Filters reduce complexity.

Filters should be

Visible

Easy to clear

Persistent

Understandable

Complex filters should support saving.

---

# Sorting

Sorting should answer common questions.

Newest

Oldest

Alphabetical

Status

Priority

Date

Users should always understand the current sorting method.

---

# Searching

Search should complement browsing.

Support

Instant search

Autocomplete

Recent searches

Saved searches

Highlighted matches

Search should feel immediate.

---

# Pagination

Choose the correct strategy.

Pagination

Load More

Infinite Scroll

Virtual Scrolling

Selection depends on user goals.

---

# Progressive Disclosure

Display summary first.

Reveal detail when requested.

Reduce visual complexity.

Support exploration.

---

# Responsive Behaviour

Information should adapt.

Desktop

Multiple columns.

Tablet

Reduced density.

Mobile

Single-column priority.

Content should reorganize,

not simply shrink.

---

# Accessibility

Data displays must support

Keyboard navigation

Screen readers

Logical reading order

Visible focus

High contrast

Accessible tables

Accessible charts

Accessibility applies to every display pattern.

---

# Performance

Optimize large datasets.

Use

Lazy loading

Virtual scrolling

Pagination

Caching

Skeleton loading

Avoid rendering unnecessary information.

---

# AI Rules

AI should

Choose the correct display pattern

Reduce visual clutter

Prioritize important information

Respect hierarchy

Support accessibility

Prefer skeleton loading

Optimize large datasets

---

# Best Practices

Display only useful information.

Use appropriate patterns.

Support comparison.

Provide context.

Keep layouts consistent.

Encourage exploration.

Optimize performance.

---

# Common Mistakes

Too much information.

No hierarchy.

Tiny tables.

Unreadable cards.

No filtering.

No search.

No empty states.

Poor responsiveness.

---

# Anti-patterns

Displaying raw database fields.

Horizontal scrolling everywhere.

Infinite nested tables.

Charts without explanation.

Meaningless statistics.

Color-only status indicators.

Displaying every available field.

Users should discover insight.

Not data overload.

---

# Implementation

Applications consume Display Patterns.

Display Patterns consume Components.

Components consume Recipes.

Recipes consume Tokens.

Applications should never invent inconsistent display behaviour.

---

# Data Display Checklist

□ Is hierarchy clear?

□ Is the correct display pattern used?

□ Can users scan quickly?

□ Are empty states helpful?

□ Are skeletons used?

□ Is filtering intuitive?

□ Is search available where needed?

□ Is accessibility supported?

□ Is performance acceptable?

□ Is the display reusable?

Only then is the display approved.

---

# Final Principle

Users should never struggle to understand information.

Great data presentation transforms information into insight.

It quietly guides attention,

supports decision-making,

reduces cognitive load,

and enables users to act with confidence.