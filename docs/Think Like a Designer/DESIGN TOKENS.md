# =============================================================================
# MODULE 3
# DESIGN TOKENS
# =============================================================================

> Design once.
> Reuse everywhere.

---

# Objective

Learn how world-class companies create scalable design systems.

By the end of this module you will stop using random CSS values.

Instead, every visual decision will come from a shared design language.

---

# What Are Design Tokens?

A design token is a named value that represents a design decision.

Instead of writing

padding: 24px;

we define

Space Large

↓

24px

Now every component uses the same value.

---

# Why Design Tokens Exist

Imagine changing every button radius across your application.

Without tokens

Search

Replace

Hope nothing breaks.

With tokens

Change one value.

Entire product updates.

Tokens create consistency.

---

# Think Like An Architect

An architect doesn't tell builders

"This wall should be roughly this wide."

They provide specifications.

Door Width

900mm

Ceiling Height

3000mm

Concrete Grade

C30

Steel Beam

Type B

These become standards.

Design tokens are the frontend equivalent.

---

# The Language of Design

Instead of speaking in pixels

Speak in meaning.

Bad

padding: 24px;

Good

Space Large

Bad

border-radius: 12px;

Good

Radius Large

Bad

#1A73E8

Good

Primary Brand Color

Meaning is more powerful than numbers.

---

# Token Categories

Our design system will define tokens for

Typography

Spacing

Colors

Radius

Shadows

Motion

Elevation

Borders

Opacity

Breakpoints

Z-Index

Containers

Everything visual becomes a token.

---

# Typography Tokens

Don't think

48px

Think

Display Large

Heading Large

Heading Medium

Body Large

Body

Caption

Label

Button

Purpose over numbers.

---

# Spacing Tokens

Never invent spacing.

Instead define levels.

2px

4px

8px

12px

16px

24px

32px

48px

64px

96px

128px

Each level has meaning.

---

# Color Tokens

Never write

#2563EB

Instead define

Primary

Primary Hover

Primary Active

Surface

Background

Border

Success

Warning

Danger

Information

Muted

Now colors become language.

---

# Radius Tokens

Tiny

Small

Medium

Large

Extra Large

Pill

Circle

Every component shares the same corner language.

---

# Shadow Tokens

Instead of random shadows

Shadow XS

Shadow Small

Shadow Medium

Shadow Large

Shadow Floating

Different elevations.

Consistent everywhere.

---

# Motion Tokens

Fast

Normal

Slow

Extra Slow

Instead of

transition: 237ms;

Every interaction feels related.

---

# Z-Index Tokens

Dropdown

Modal

Toast

Tooltip

Overlay

Never use

z-index: 999999;

Again.

---

# Breakpoint Tokens

Mobile

Tablet

Laptop

Desktop

Ultra Wide

Layouts become predictable.

---

# Container Tokens

Reading

Content

Wide

Full Width

Hero

Dashboard

Different content needs different widths.

---

# Why Tokens Matter

Imagine building

Homepage

Dashboard

Portal

Filament

Admin

Knowledge Centre

Project Viewer

Quotation Wizard

Without tokens

Every page slowly becomes different.

With tokens

Everything feels like one product.

---

# Tokens Build Trust

Users don't consciously notice consistency.

But they feel it.

Consistent products

Feel reliable.

Professional.

Premium.

---

# Tokens Before Components

Never build

Button

Card

Modal

Table

First.

Build

Colors

Spacing

Typography

Motion

Radius

Then components become easy.

---

# Token Hierarchy

Foundation

↓

Design Tokens

↓

Components

↓

Layouts

↓

Pages

↓

Products

Never reverse this order.

---

# Zytech Philosophy

Every visual decision should come from one shared language.

The public website.

The client portal.

The dashboard.

The quotation wizard.

Filament.

Everything should feel like one ecosystem.

---

# Exercise

Open Stripe.

Choose one button.

Ask yourself

What spacing token?

What radius token?

What typography token?

What shadow token?

What motion token?

What color token?

Now repeat for

Cards

Forms

Navigation

Hero

Footer

You'll discover that almost every element reuses the same small vocabulary.

---

# Key Takeaways

✓ Tokens eliminate randomness.

✓ Tokens create consistency.

✓ Tokens improve maintainability.

✓ Tokens improve scalability.

✓ Tokens improve collaboration.

✓ Tokens build premium products.

✓ Design systems begin with tokens, not components.
