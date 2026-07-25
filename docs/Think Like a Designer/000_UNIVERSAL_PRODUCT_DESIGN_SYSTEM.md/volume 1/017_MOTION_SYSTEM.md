# =============================================================================
# UNIVERSAL PRODUCT DESIGN SYSTEM (UPDS)
# =============================================================================
# 017_MOTION_SYSTEM.md
# =============================================================================

Version 1.0

---

# Table of Contents

1. Purpose

2. Motion Philosophy

3. Motion Principles

4. Motion Hierarchy

5. Motion Categories

6. Motion Tokens

7. Timing

8. Easing

9. Interaction Motion

10. Navigation Motion

11. Page Transitions

12. Component Motion

13. Loading Motion

14. Skeleton Motion

15. Feedback Motion

16. Scroll Motion

17. Micro-interactions

18. Accessibility

19. Performance

20. AI Rules

21. Best Practices

22. Common Mistakes

23. Anti-patterns

24. Implementation

25. Checklist

---

# Purpose

Motion communicates change.

Motion should never exist for decoration.

It should communicate

State

Hierarchy

Cause and effect

Feedback

Navigation

Attention

Continuity

Motion reduces uncertainty.

---

# Motion Philosophy

Every animation should answer at least one question.

What changed?

Where did it come from?

Where is it going?

Can I interact with it?

If motion cannot answer one of these questions,

it should not exist.

---

# Motion Principles

Motion should be

Intentional

Fast

Natural

Predictable

Subtle

Accessible

Consistent

Avoid unnecessary movement.

Premium interfaces move with confidence.

---

# Motion Hierarchy

Not every element deserves motion.

Highest Priority

Dialogs

Drawers

Navigation

Notifications

Page transitions

Medium Priority

Buttons

Cards

Menus

Tabs

Accordions

Low Priority

Icons

Decorative elements

Background effects

Motion should reinforce hierarchy.

---

# Motion Categories

Entrance

Exit

Transition

Hover

Focus

Pressed

Loading

Skeleton

Scrolling

Drag

Drop

Expand

Collapse

Feedback

Success

Error

Warning

Navigation

Each category has a defined purpose.

---

# Motion Tokens

Every animation belongs to the system.

Never invent random animations.

Motion Tokens define

Duration

Delay

Curve

Scale

Opacity

Rotation

Translation

Blur

Each component consumes Motion Tokens.

---

# Timing

Fast

Immediate feedback.

Medium

Most interactions.

Slow

Major transitions.

Very Slow

Rare storytelling moments.

Users should never wait for animation.

Animation serves the user.

---

# Easing

Motion should accelerate and decelerate naturally.

Avoid linear animation for interface interactions.

Use easing consistently.

Motion should feel physical.

Not robotic.

---

# Interaction Motion

Hover

Communicates availability.

Pressed

Communicates action.

Focused

Communicates accessibility.

Selected

Communicates state.

Disabled

Communicates restriction.

Interaction should never surprise users.

---

# Navigation Motion

Navigation should preserve orientation.

Menus

Drawers

Sidebars

Command Palettes

Breadcrumbs

Tabs

Users should always know where they came from.

---

# Page Transitions

Transitions should communicate continuity.

Pages should feel connected.

Avoid dramatic effects.

Prefer subtle movement.

Maintain context whenever possible.

---

# Component Motion

Every component should define

Entrance

Exit

Hover

Focus

Active

Disabled

Loading

Error

Success

Components should behave consistently.

---

# Loading Motion

Loading should reassure users.

Avoid unnecessary spinners.

Prefer

Progress indicators

Skeletons

Optimistic updates

Motion should reduce perceived waiting time.

---

# Skeleton Motion

Skeletons preserve layout stability.

Skeletons communicate

Content is coming.

The interface is functioning.

The user should not lose orientation.

Prefer skeletons over generic loading indicators.

---

# Feedback Motion

Success

Confirms completion.

Warning

Draws attention.

Error

Communicates interruption.

Feedback should be immediate.

Never exaggerated.

---

# Scroll Motion

Scrolling should remain smooth.

Effects should enhance reading.

Avoid excessive parallax.

Avoid scroll hijacking.

Content remains the priority.

---

# Micro-interactions

Micro-interactions make interfaces feel responsive.

Examples

Button press

Checkbox selection

Toggle switch

Input focus

Toast appearance

Card hover

Navigation highlight

Micro-interactions should feel effortless.

---

# Accessibility

Respect reduced motion preferences.

Provide alternatives for users sensitive to animation.

Never communicate important information using motion alone.

Support keyboard users equally.

Accessibility always overrides aesthetics.

---

# Performance

Prefer transform and opacity animations.

Avoid animating layout properties unnecessarily.

Minimize expensive effects.

Maintain smooth performance.

Motion should never reduce usability.

---

# AI Rules

AI should

Reuse Motion Tokens

Never invent animations

Respect accessibility

Prefer subtle transitions

Avoid excessive motion

Maintain consistency

---

# Best Practices

Animate purposefully.

Keep durations short.

Support user understanding.

Maintain consistency.

Reduce distractions.

Design for accessibility.

Think in transitions.

---

# Common Mistakes

Animating everything.

Slow interfaces.

Long delays.

Inconsistent timing.

Random easing.

Heavy parallax.

Decorative motion.

Ignoring accessibility.

---

# Anti-patterns

Bouncing buttons.

Infinite animations.

Flashing content.

Rotating cards.

Spinning navigation.

Large zoom effects.

Animation without purpose.

Motion should support the interface.

Never compete with it.

---

# Implementation

Applications consume Motion Tokens.

Components consume Motion Recipes.

Patterns compose motion.

Themes remain consistent.

Applications never invent animation styles.

---

# Motion Checklist

□ Does the motion communicate change?

□ Is the animation necessary?

□ Is timing consistent?

□ Is easing consistent?

□ Does it respect accessibility?

□ Does it perform well?

□ Does it preserve context?

□ Is it reusable?

□ Is it documented?

□ Is it consistent with the system?

Only then is motion approved.

---

# Final Principle

The best motion is barely noticed.

Users should simply feel that the interface is responsive,
predictable,
and alive.

Great motion creates confidence.

It quietly explains change,

reinforces hierarchy,

supports interaction,

and makes every experience feel refined.
