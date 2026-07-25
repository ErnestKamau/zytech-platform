# =============================================================================
# UNIVERSAL PRODUCT DESIGN SYSTEM (UPDS)
# =============================================================================
# 026_FEEDBACK_SYSTEM.md
# =============================================================================

Version 1.0

---

# Table of Contents

1. Purpose

2. Feedback Philosophy

3. Feedback Principles

4. Feedback Hierarchy

5. Feedback Categories

6. Success Feedback

7. Error Feedback

8. Warning Feedback

9. Informational Feedback

10. Progress Feedback

11. Loading Feedback

12. Skeleton Feedback

13. Empty State Feedback

14. Confirmation Feedback

15. Undo Patterns

16. Notification System

17. Feedback Tone

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

Feedback is how applications communicate with users.

Every user action deserves an appropriate response.

Users should never wonder

Did it work?

Is it still working?

What should I do next?

Feedback reduces uncertainty.

---

# Feedback Philosophy

Feedback should be

Immediate

Clear

Useful

Respectful

Actionable

Feedback should help users move forward.

Not interrupt them.

---

# Feedback Principles

Every feedback message should answer

What happened?

Why did it happen?

What can I do next?

If no action is required,

say so.

---

# Feedback Hierarchy

Critical

↓

Error

↓

Warning

↓

Success

↓

Information

↓

Passive Status

The visual emphasis should match importance.

---

# Feedback Categories

Success

Information

Warning

Error

Loading

Progress

Confirmation

Undo

Empty

Offline

Maintenance

Permission

Validation

Every category has a defined purpose.

---

# Success Feedback

Success confirms completed work.

Examples

Saved successfully.

Project created.

Changes published.

Invoice generated.

Quote submitted.

Keep success messages short.

Avoid unnecessary celebration.

---

# Error Feedback

Errors explain

What failed

Why

How to recover

Never blame users.

Never expose technical messages.

Provide actionable guidance.

---

# Warning Feedback

Warnings communicate potential risk.

Examples

Unsaved changes.

File size is large.

Deleting this project cannot be undone.

Warnings allow users to make informed decisions.

---

# Informational Feedback

Information communicates neutral updates.

Examples

A new version is available.

Maintenance starts at midnight.

A report is processing.

Informational messages should never feel alarming.

---

# Progress Feedback

Long operations require progress.

Examples

Uploading

Importing

Generating

Syncing

Analyzing

Users should understand

Current progress

Estimated completion

Remaining work

---

# Loading Feedback

Loading communicates temporary waiting.

Prefer

Skeletons

Progress Bars

Optimistic Updates

Avoid unnecessary spinners.

Loading should preserve layout stability.

---

# Skeleton Feedback

Skeletons communicate

Content is coming.

The interface is working.

Layout remains stable.

Skeletons should resemble final content.

Avoid generic placeholder blocks.

---

# Empty State Feedback

Empty states should educate.

Explain

Why nothing appears

How to add content

What happens next

Empty states should encourage action.

---

# Confirmation Feedback

Confirm only high-risk actions.

Examples

Delete Project

Archive Report

Approve Results

Cancel Order

Avoid confirmation fatigue.

Not every action needs confirmation.

---

# Undo Patterns

Whenever possible,

prefer Undo over confirmation.

Undo is faster.

Undo reduces interruptions.

Undo builds confidence.

Users recover naturally.

---

# Notification System

Notifications should have priorities.

Critical

High

Medium

Low

Silent

Notifications should never compete for attention.

Only the most important deserves interruption.

---

# Feedback Tone

Tone should be

Professional

Friendly

Confident

Helpful

Human

Avoid sarcasm.

Avoid blaming users.

Avoid robotic language.

---

# Accessibility

Feedback must support

Screen readers

Keyboard navigation

Reduced motion

Visible focus

High contrast

Color-independent communication

Never rely only on color.

---

# Performance

Feedback should appear immediately.

Avoid blocking interactions.

Dismiss automatically when appropriate.

Persist important messages.

Never delay confirmations.

---

# AI Rules

AI should

Choose appropriate feedback

Match severity

Avoid unnecessary notifications

Prefer undo

Maintain consistent tone

Respect accessibility

Never invent inconsistent messaging

---

# Best Practices

Be concise.

Be helpful.

Show feedback immediately.

Provide recovery options.

Celebrate meaningful success.

Use skeletons.

Prefer undo.

Reduce interruptions.

---

# Common Mistakes

Generic error messages.

Technical jargon.

Delayed feedback.

Too many notifications.

Confirmation for every action.

Long success messages.

Hidden validation.

---

# Anti-patterns

"You did something wrong."

"Unknown Error."

"Operation Failed."

Infinite loading.

Spinner-only interfaces.

Multiple simultaneous toasts.

Notifications covering important content.

Feedback should guide.

Never frustrate.

---

# Implementation

Applications consume Feedback Patterns.

Feedback Patterns consume Components.

Components consume Recipes.

Recipes consume Tokens.

Applications should never invent inconsistent feedback.

---

# Feedback Checklist

□ Does feedback appear immediately?

□ Is the message clear?

□ Is recovery possible?

□ Is severity appropriate?

□ Is accessibility supported?

□ Is tone consistent?

□ Does it avoid unnecessary interruption?

□ Is Undo supported where appropriate?

□ Can AI classify the feedback correctly?

□ Is it reusable?

Only then is the feedback approved.

---

# Final Principle

Feedback is the conversation after every interaction.

Great products reassure users,

explain problems clearly,

celebrate success appropriately,

provide recovery when things go wrong,

and communicate with confidence,

clarity,

and respect.