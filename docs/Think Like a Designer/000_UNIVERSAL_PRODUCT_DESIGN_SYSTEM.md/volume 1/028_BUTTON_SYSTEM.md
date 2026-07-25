# =============================================================================
# UNIVERSAL PRODUCT DESIGN SYSTEM (UPDS)
# =============================================================================
# 028_BUTTON_SYSTEM.md
# =============================================================================

Version 1.0

---

# Table of Contents

1. Purpose

2. Button Philosophy

3. Button Principles

4. Button Hierarchy

5. Button Anatomy

6. Button Variants

7. Button Sizes

8. Button States

9. Button Composition

10. Icon Buttons

11. Split Buttons

12. Floating Action Buttons

13. Button Groups

14. Loading Buttons

15. Glass Buttons

16. Motion

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

Buttons represent intentional user actions.

Every button asks the user to make a decision.

Buttons should communicate

Importance

Priority

Outcome

Confidence

Every button should encourage action without demanding attention.

---

# Button Philosophy

Buttons are not decoration.

Buttons are invitations to interact.

Users should immediately understand

Can I click it?

What will happen?

Is this the primary action?

Button appearance should answer these questions.

---

# Button Principles

Buttons should be

Consistent

Accessible

Predictable

Responsive

Comfortable

Reusable

Purpose-driven

Never design a button for a single page.

Design it for every product.

---

# Button Hierarchy

Every interface should define a clear action hierarchy.

Primary

The single most important action.

Examples

Submit

Continue

Save

Create

Request Quote

Secondary

Supporting actions.

Examples

Cancel

Back

Preview

View Details

Tertiary

Low-emphasis actions.

Examples

Learn More

Read More

Expand

Ghost

Minimal emphasis.

Used where interface chrome should remain minimal.

Danger

Destructive actions.

Delete

Archive

Remove

Reset

Success

Positive confirmation actions.

Approve

Publish

Complete

Info

Neutral actions.

View

Inspect

Details

Warning

Potentially risky actions.

Suspend

Deactivate

Overwrite

Every screen should contain only one Primary Button.

---

# Button Anatomy

Every button contains

Container

↓

Label

↓

Optional Icon

↓

Optional Badge

↓

Optional Loading Indicator

↓

Interaction Layer

↓

Focus Ring

Each element has one responsibility.

---

# Button Variants

Filled

Outlined

Ghost

Text

Glass

Gradient

Soft

Icon

Link

FAB

Split

Segmented

Applications should use documented variants only.

---

# Button Sizes

Extra Small

Small

Medium

Large

Extra Large

Every size inherits

Spacing Tokens

Typography Tokens

Radius Tokens

Motion Tokens

Never hardcode dimensions.

---

# Button States

Default

Hover

Focus

Pressed

Loading

Disabled

Selected

Active

Success

Warning

Error

Every state must be documented.

---

# Button Composition

Buttons consume

Color Tokens

Typography Tokens

Spacing Tokens

Radius Tokens

Elevation Tokens

Motion Tokens

Interaction Tokens

Buttons never define their own styling.

---

# Icon Buttons

Icon-only buttons require

Accessible labels

Large touch targets

Visible focus

Clear meaning

Never rely solely on icons.

---

# Split Buttons

Split buttons combine

Primary Action

↓

Secondary Options

Best used for

Export

Share

Create

Download

Avoid unnecessary complexity.

---

# Floating Action Buttons

FABs represent one highly important action.

Examples

New Project

Create Sample

Add Delivery

Compose

Only one FAB should exist within a context.

---

# Button Groups

Button groups organize related actions.

Maintain

Equal spacing

Clear hierarchy

Consistent sizing

Logical ordering

Avoid overcrowding.

---

# Loading Buttons

During loading

Disable repeated clicks.

Preserve width.

Replace content with a spinner or progress indicator.

Never change button size while loading.

---

# Glass Buttons

Glass buttons inherit the Glass Recipe.

Characteristics

Low-opacity surface

Backdrop blur

Soft border

Inset highlight

Subtle shadow

Optional shimmer sweep

Hover spotlight

Glass buttons should appear premium,

not transparent.

---

# Motion

Buttons should animate naturally.

Supported animations

Opacity

Elevation

Scale (subtle)

Shimmer Sweep

Glass Spotlight

Ripple (optional)

Focus Ring

Motion should reinforce interaction,

never distract.

---

# Accessibility

Buttons must support

Keyboard navigation

Visible focus

Touch accessibility

Screen readers

Reduced motion

High contrast

Every button should remain usable without a mouse.

---

# Performance

Buttons should

Render instantly

Reuse tokens

Avoid unnecessary animations

Minimize layout shifts

Avoid expensive visual effects

Premium should never mean slow.

---

# AI Rules

AI should

Reuse existing button recipes

Respect hierarchy

Prefer one primary action

Never invent random variants

Maintain accessibility

Use glass variants intentionally

Never duplicate button styles

---

# Best Practices

Use one primary action.

Write concise labels.

Support keyboard users.

Use icons consistently.

Animate subtly.

Reuse button recipes.

Design for every product.

---

# Common Mistakes

Too many primary buttons.

Tiny click areas.

Vague labels.

Inconsistent sizes.

Random colors.

Missing loading states.

Missing focus states.

---

# Anti-patterns

Clickable text pretending to be buttons.

Gradient overload.

Heavy shadows.

Tiny icon buttons.

Multiple floating action buttons.

Glass everywhere.

Buttons competing for attention.

Buttons should guide action.

Never create confusion.

---

# Implementation

Applications consume Button Recipes.

Buttons consume

Color Tokens

Typography Tokens

Spacing Tokens

Radius Tokens

Motion Tokens

Elevation Tokens

Interaction Tokens

Applications should never create undocumented button styles.

---

# Button Checklist

□ Is the hierarchy correct?

□ Is the label clear?

□ Is accessibility supported?

□ Are all states defined?

□ Does it consume tokens?

□ Is motion subtle?

□ Is the touch target large enough?

□ Does it support keyboard navigation?

□ Can AI classify the button correctly?

□ Is it reusable?

Only then is the button approved.

---

# Final Principle

Buttons are promises.

Every button promises that something meaningful will happen.

Great buttons inspire confidence,

communicate priority,

feel effortless to interact with,

and become a consistent language across every product in the ecosystem.