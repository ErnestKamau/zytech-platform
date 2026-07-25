# =============================================================================
# UNIVERSAL PRODUCT DESIGN SYSTEM (UPDS)
# =============================================================================
# 005_DESIGN_TOKENS.md
# =============================================================================

> Design Tokens are the DNA of every product.
>
> Components are temporary.
>
> Tokens are permanent.

Version 1.0

---

# Purpose

Design Tokens are the smallest reusable design decisions in the system.

A token should never describe implementation.

A token should describe intent.

Good

Primary Surface

Danger Border

Large Radius

Glass Blur

Heading Large

Small Spacing

Bad

Blue

16px

Border Radius 12

Arial

Hardcoded values belong to primitive tokens.

Components should consume semantic tokens.

---

# The Design Hierarchy

Everything in UPDS follows this hierarchy.

User Experience

↓

Design Principles

↓

Visual Language

↓

Tokens

↓

Recipes

↓

States

↓

Components

↓

Patterns

↓

Templates

↓

Pages

↓

Applications

Tokens are the bridge between design and implementation.

---

# What Is A Token?

A Design Token represents one design decision.

Examples

Color

Spacing

Typography

Radius

Elevation

Animation

Blur

Shadow

Border

Opacity

Duration

Breakpoint

Container

Icon Size

Image Ratio

Aspect Ratio

Grid

Surface

Layer

Every design decision should become a token before becoming code.

---

# The Five Token Levels

UPDS organizes tokens into five layers.

Primitive

↓

Semantic

↓

Component

↓

Recipe

↓

Brand

Each layer has a specific responsibility.

Never skip layers.

---

# Level 1 — Primitive Tokens

Primitive Tokens are raw values.

They contain no meaning.

Examples

Blue 500

Blue 600

8px

16px

24px

48px

4ms

200ms

Blur 12

Radius 16

Opacity 80%

Primitive tokens should almost never be referenced directly by components.

They exist only as building blocks.

---

# Level 2 — Semantic Tokens

Semantic Tokens describe purpose.

Examples

Primary

Secondary

Surface

Background

Glass Surface

Text Primary

Text Secondary

Success

Warning

Danger

Info

Muted

Accent

Interactive

Disabled

Focus

Hover

Semantic tokens allow complete theme changes without touching components.

---

# Level 3 — Component Tokens

Each component defines its own token family.

Example

Button

Height

Padding

Gap

Icon Size

Radius

Shadow

Animation

Font

Border

Background

Focus Ring

The Button never knows colors.

It asks semantic tokens.

---

# Level 4 — Recipe Tokens

Recipes combine multiple component tokens into reusable design patterns.

Example

Glass Button

↓

Glass Surface

↓

Primary Text

↓

Medium Radius

↓

Floating Shadow

↓

Hover Lift

↓

Spotlight Effect

↓

Glass Border

Instead of designing Glass Buttons repeatedly,

we reuse the recipe.

---

# Level 5 — Brand Tokens

Brand Tokens customize the design language for individual products.

Examples

Zytech

Primary

Steel Blue

Accent

Construction Orange

Neutral

Concrete Gray

Khat Delivery

Primary

Organic Green

Accent

Fresh Lime

Neutral

Warm Sand

Medical Platform

Primary

Medical Blue

Accent

Trust Cyan

Neutral

Slate Gray

Every product shares the same components.

Only the brand tokens change.

---

# Token Families

Tokens are grouped into families.

Color

Typography

Spacing

Radius

Elevation

Glass

Shadow

Blur

Border

Motion

Duration

Opacity

Grid

Containers

Breakpoints

Icons

Assets

Images

Video

Transitions

Every family follows the same structure.

---

# Naming Philosophy

Token names describe purpose,

not implementation.

Good

surface.glass.soft

surface.default

surface.hero

text.primary

text.secondary

button.primary.background

spacing.section.large

spacing.card.medium

Bad

blue500

gray100

border12

padding18

redBackground

The meaning should survive a rebrand.

---

# Token Metadata

Every token includes documentation.

Name

Category

Purpose

Usage

Avoid

Accessibility

Platform Support

Recipes

Components

Examples

Every token teaches.

Not just stores values.

---

# Token Relationships

Tokens build upon one another.

Primitive

↓

Semantic

↓

Component

↓

Recipe

↓

Brand

Components should never reference Primitive Tokens directly.

They consume Component Tokens.

Component Tokens consume Semantic Tokens.

Semantic Tokens consume Primitive Tokens.

---

# Token Versioning

Tokens are versioned.

Major

Breaking changes.

Minor

New token families.

Patch

Corrections and documentation improvements.

Every product should know which version it implements.

---

# Theme Independence

Tokens should support

Light

Dark

High Contrast

Glass

Reduced Motion

Future Themes

without changing components.

Only token values change.

---

# AI Rules

Artificial Intelligence should never invent tokens.

Before creating a new token,

AI must ask

Does this already exist?

Can an existing semantic token solve this?

Can an existing recipe solve this?

Can an existing component consume this?

If yes,

reuse.

If no,

propose a new token with documentation.

---

# Token Lifecycle

Every token follows a lifecycle.

Draft

↓

Review

↓

Approved

↓

Released

↓

Deprecated

↓

Archived

Deprecated tokens should never disappear immediately.

Migration guidance must always exist.

---

# Token Quality Checklist

Before approving a token verify

□ Name is semantic

□ Purpose is clear

□ Platform independent

□ Accessibility considered

□ Brand independent

□ Reusable

□ AI documented

□ Recipes reference it

□ Components consume it

□ Examples included

Only then is the token ready.

---

# The Golden Rule

Tokens describe design decisions.

Components consume tokens.

Recipes compose components.

Applications compose recipes.

Never hardcode design decisions inside components.
