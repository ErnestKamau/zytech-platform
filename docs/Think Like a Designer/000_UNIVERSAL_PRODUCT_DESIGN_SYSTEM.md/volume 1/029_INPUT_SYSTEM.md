# =============================================================================
# UNIVERSAL PRODUCT DESIGN SYSTEM (UPDS)
# =============================================================================
# 029_INPUT_SYSTEM.md
# =============================================================================

Version 1.0

---

# Table of Contents

1. Purpose

2. Input Philosophy

3. Input Principles

4. Input Hierarchy

5. Input Anatomy

6. Input Categories

7. Input Variants

8. Input Sizes

9. Labels

10. Placeholders

11. Supporting Text

12. Prefixes & Suffixes

13. Validation

14. Input States

15. Smart Inputs

16. Input Groups

17. Specialized Inputs

18. Glass Inputs

19. Motion

20. Accessibility

21. Performance

22. AI Rules

23. Best Practices

24. Common Mistakes

25. Anti-patterns

26. Implementation

27. Checklist

---

# Purpose

Inputs collect information.

Every input represents a conversation between the user and the application.

Applications should minimize effort while maximizing clarity.

Every input should have a purpose.

---

# Input Philosophy

Users should never struggle to understand

What information is required

Why it is needed

How it should be entered

Inputs should reduce thinking.

Not increase it.

---

# Input Principles

Inputs should be

Clear

Predictable

Accessible

Responsive

Forgiving

Reusable

Consistent

Every input should feel familiar.

---

# Input Hierarchy

Form

↓

Section

↓

Field Group

↓

Input

↓

Validation

↓

Feedback

↓

Submission

Every level has a defined responsibility.

---

# Input Anatomy

Every input consists of

Container

↓

Label

↓

Input Surface

↓

Placeholder

↓

Supporting Text

↓

Leading Element

↓

Trailing Element

↓

Validation Message

↓

Focus Indicator

↓

State Layer

Every element communicates something.

---

# Input Categories

Text

Email

Password

Phone

Search

URL

Number

Currency

Percentage

Date

Time

Date Range

Textarea

Rich Text

File Upload

Image Upload

Video Upload

Audio Upload

Signature

Location

Color Picker

Slider

Rating

OTP

Verification Code

Barcode

QR Scanner

Autocomplete

Tags

Select

Multi Select

Tree Select

Each category solves a specific problem.

---

# Input Variants

Filled

Outlined

Underlined

Ghost

Glass

Soft

Minimal

Compact

Applications should only use approved variants.

---

# Input Sizes

Extra Small

Small

Medium

Large

Extra Large

Sizing should come from design tokens.

Never hardcode dimensions.

---

# Labels

Every input requires a persistent label.

Labels should

Remain visible

Describe the field

Use natural language

Avoid technical terminology

Labels should never disappear.

---

# Placeholders

Placeholders provide examples.

Examples

John Doe

Search projects...

KES 25,000

example@email.com

They should supplement labels,

never replace them.

---

# Supporting Text

Supporting text explains

Requirements

Limits

Examples

Recommendations

Show supporting text only when useful.

Avoid unnecessary clutter.

---

# Prefixes & Suffixes

Examples

$

KES

%

kg

km

https://

@

Calendar Icon

Search Icon

Visibility Toggle

Units should reduce ambiguity.

---

# Validation

Validation should happen progressively.

Immediate

↓

Field

↓

Section

↓

Form

↓

Submission

Never wait until submission to reveal obvious errors.

---

# Input States

Default

Hover

Focus

Typing

Filled

Success

Warning

Error

Disabled

Readonly

Loading

Skeleton

Every state must be designed.

---

# Smart Inputs

Applications should reduce typing.

Examples

Autocomplete

Suggestions

Recent Values

Saved Addresses

Frequently Used Projects

AI Assistance

Smart Defaults

Smart inputs improve productivity.

---

# Input Groups

Related inputs should be grouped.

Examples

Address

Phone Number

Dimensions

Currency

Measurements

Coordinates

Date Range

Logical grouping reduces cognitive load.

---

# Specialized Inputs

Construction

Measurements

Blueprint Upload

GPS Coordinates

Project Codes

Laboratory

Sample ID

Equipment

Reference Range

Collection Date

Delivery

Tracking Code

Driver Selection

Pickup Point

Destination

Products should define reusable specialized inputs.

---

# Glass Inputs

Glass inputs inherit the Glass Recipe.

Characteristics

Low-opacity surface

Backdrop blur

Soft border

Inset highlight

Subtle reflections

Hover spotlight

Gentle elevation

Glass inputs should remain readable under all lighting conditions.

---

# Motion

Inputs should animate naturally.

Supported motion

Focus Glow

Border Transition

Glass Spotlight

Floating Labels

Success Transition

Error Shake (subtle)

Loading Progress

Motion should communicate state,

not decoration.

---

# Accessibility

Inputs must support

Keyboard navigation

Visible focus

Screen readers

Large touch targets

Reduced motion

High contrast

Accessible validation

Accessibility begins with clarity.

---

# Performance

Inputs should

Respond instantly

Debounce expensive validation

Lazy-load suggestions

Optimize autocomplete

Preserve typing performance

Never block user interaction.

---

# AI Rules

AI should

Choose the correct input type

Reuse existing recipes

Reduce typing

Recommend smart defaults

Preserve accessibility

Maintain consistency

Avoid unnecessary fields

---

# Best Practices

Use the correct input type.

Keep labels visible.

Validate early.

Provide examples.

Support autocomplete.

Preserve entered data.

Group related fields.

---

# Common Mistakes

Placeholder-only labels.

Late validation.

Tiny click targets.

Overly strict validation.

Excessive required fields.

Poor grouping.

Technical terminology.

---

# Anti-patterns

Blocking copy and paste.

Blocking password managers.

Resetting user input.

Destroying entered data.

Hidden validation.

Unexpected formatting.

Input designs that differ between pages.

Inputs should feel predictable.

Never surprising.

---

# Implementation

Applications consume Input Recipes.

Input Recipes consume

Color Tokens

Spacing Tokens

Typography Tokens

Radius Tokens

Motion Tokens

Interaction Tokens

Accessibility Tokens

Applications should never invent undocumented input styles.

---

# Input Checklist

□ Is the correct input type used?

□ Is the label visible?

□ Are examples provided?

□ Is validation immediate?

□ Are all states defined?

□ Is accessibility supported?

□ Is performance acceptable?

□ Does it consume design tokens?

□ Can AI classify the input correctly?

□ Is it reusable?

Only then is the input approved.

---

# Final Principle

Inputs are conversations.

Every field asks a question.

Great inputs reduce effort,

prevent mistakes,

encourage confidence,

and quietly guide users toward successful completion without unnecessary friction.