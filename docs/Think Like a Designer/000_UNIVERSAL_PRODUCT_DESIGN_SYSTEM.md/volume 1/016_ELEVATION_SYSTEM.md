# =============================================================================
# UNIVERSAL PRODUCT DESIGN SYSTEM (UPDS)
# =============================================================================
# 016_ELEVATION_SYSTEM.md
# =============================================================================

Version 1.0

---

# Table of Contents

1. Purpose

2. Elevation Philosophy

3. Perceived Depth

4. Elevation Hierarchy

5. Elevation Recipes

6. Shadow System

7. Lighting System

8. Blur & Atmosphere

9. Hover Elevation

10. Focus Elevation

11. Active Elevation

12. Motion & Elevation

13. Glass Elevation

14. Dark Theme Elevation

15. Accessibility

16. Performance

17. AI Rules

18. Best Practices

19. Common Mistakes

20. Anti-patterns

21. Theme Studio

22. Implementation

23. Checklist

---

# Purpose

Elevation creates spatial understanding.

Users should immediately understand

what is behind,

what is above,

what is interactive,

what is floating,

and what is currently active.

Elevation communicates hierarchy.

Not decoration.

---

# Elevation Philosophy

Digital interfaces exist in three dimensions.

Every component occupies

X

Y

and

Z.

Z represents importance.

The closer a surface feels,

the more attention it receives.

Elevation should feel physical.

Not artificial.

---

# Perceived Depth

Depth is never created by shadows alone.

Depth is the combination of

Surface

Contrast

Lighting

Blur

Motion

Shadow

Scale

Opacity

Border

Ambient Color

Every elevated component should use multiple depth cues.

---

# Elevation Hierarchy

ELV-000

Background

ELV-100

Surface

ELV-200

Card

ELV-300

Raised Card

ELV-400

Navigation

ELV-500

Drawer

ELV-600

Modal

ELV-700

Popover

ELV-800

Tooltip

ELV-900

Critical Overlay

Applications should never invent new elevation levels.

---

# Elevation Recipes

Each elevation recipe defines

Shadow

Border

Surface

Blur

Lighting

Opacity

Motion

Glass

Dark Mode

Accessibility

Performance Budget

Elevation becomes reusable.

---

# Shadow System

Shadows communicate separation.

Good shadows are

Soft

Layered

Low opacity

Consistent

Avoid

Black shadows

Sharp shadows

Large blur radii

Heavy opacity

Shadow should support hierarchy.

Not dominate it.

---

# Lighting System

Every application assumes one virtual light source.

Lighting should remain consistent.

Surfaces closer to users receive

More highlight

Greater contrast

Slightly brighter edges

Lighting creates realism.

---

# Blur & Atmosphere

Blur communicates distance.

Closer layers

↓

Less blur

Further layers

↓

More atmospheric blur

Glass surfaces consume blur recipes.

Never invent blur values.

---

# Hover Elevation

Hover should communicate

Availability

Confidence

Responsiveness

Hover may use

Small translation

Lighting increase

Shadow refinement

Border refinement

Scale

Hover should never feel jumpy.

---

# Focus Elevation

Focused components become easier to locate.

Focus should prioritize accessibility.

Never rely solely on color.

Use

Outline

Glow

Elevation

Motion

Together.

---

# Active Elevation

Pressed elements move toward the surface.

Buttons

Cards

Navigation

Controls

Active states should feel tactile.

---

# Motion & Elevation

Motion reinforces depth.

Higher surfaces

Move slower.

Lower surfaces

Move faster.

Motion should communicate spatial relationships.

---

# Glass Elevation

Glass surfaces float above standard surfaces.

Glass recipes include

Blur

Ambient light

Inner highlight

Soft border

Minimal shadow

Glass should never become visually heavy.

---

# Dark Theme Elevation

Dark themes require stronger material separation.

Avoid increasing shadow opacity.

Increase contrast through

Surface

Lighting

Border

Motion

Glass

Dark elevation should remain elegant.

---

# Accessibility

Elevation must never replace accessibility.

Every elevated component must remain

Readable

Navigable

Focusable

Understandable

Support reduced motion preferences.

---

# Performance

Limit expensive effects.

Reuse shadow recipes.

Reuse blur recipes.

Prefer GPU-friendly transforms.

Avoid unnecessary repainting.

Premium products should remain performant.

---

# AI Rules

AI should

Reuse elevation recipes.

Never invent shadows.

Respect hierarchy.

Respect performance budgets.

Preserve accessibility.

---

# Best Practices

Use elevation sparingly.

Create believable depth.

Support interaction.

Think architecturally.

Think physically.

---

# Common Mistakes

Everything elevated.

Nothing elevated.

Heavy shadows.

Large blur.

Inconsistent lighting.

Random hover effects.

---

# Anti-patterns

Neon shadows.

Floating every card.

Strong glow.

Glass everywhere.

Multiple light directions.

Shadow-only elevation.

Elevation should communicate importance.

Never decoration.

---

# Theme Studio

Elevation Explorer

Shadow Playground

Lighting Simulator

Hover Preview

Dark Mode Preview

Glass Preview

Performance Inspector

Accessibility Audit

AI Inspector

Live Playground

---

# Implementation

Applications consume Elevation Recipes.

Components consume Elevation Tokens.

Patterns consume Components.

Layouts consume Patterns.

Applications never invent elevation.

---

# Elevation Checklist

□ Does elevation communicate hierarchy?

□ Does it improve usability?

□ Does it preserve accessibility?

□ Is motion consistent?

□ Does it support dark mode?

□ Does it support glass?

□ Is performance acceptable?

□ Is it reusable?

□ Is documentation complete?

Only then is elevation approved.

---

# Final Principle

Users should never notice elevation.

They should simply understand the spatial relationship between every element.

Great elevation makes interfaces feel physical,

calm,

intentional,

and premium.