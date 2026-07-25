# =============================================================================
# UNIVERSAL PRODUCT DESIGN SYSTEM (UPDS)
# =============================================================================
# 007_RECIPE_SYSTEM.md
# =============================================================================

> Components are assembled.
>
> Never redesigned.

Version 1.0

---

# Purpose

Recipes define how Design Tokens combine into reusable interface patterns.

Recipes prevent teams from repeatedly making the same design decisions.

Instead of designing another

Button

Card

Toast

Modal

Input

Dropdown

the Design System provides approved recipes.

Applications compose recipes.

Recipes compose tokens.

---

# What Is A Recipe?

A Recipe is a collection of design decisions.

It combines

Tokens

↓

States

↓

Motion

↓

Accessibility

↓

Interactions

↓

Assets

↓

Component Rules

into one reusable blueprint.

Recipes never contain business logic.

Recipes only describe presentation.

---

# Design Hierarchy

Primitive Tokens

↓

Semantic Tokens

↓

Component Tokens

↓

Recipes

↓

Patterns

↓

Templates

↓

Applications

---

# Why Recipes Exist

Without recipes

Every project invents

another button

another toast

another modal

another card

Eventually

every application feels different.

Recipes eliminate unnecessary design decisions.

---

# Recipe Philosophy

Every Recipe should

Be reusable

Be composable

Be documented

Support theming

Support accessibility

Support dark mode

Support responsive layouts

Support platform implementations

Support AI generation

---

# Recipe Categories

Surfaces

Buttons

Forms

Cards

Navigation

Feedback

Data Display

Media

Layout

Marketing

Commerce

Mapping

Charts

Authentication

Productivity

Utilities

---

# Recipe Structure

Every Recipe contains

Name

Purpose

Problem Solved

Variants

States

Tokens Used

Motion

Accessibility

Responsive Rules

Platform Notes

AI Rules

Examples

Version

---

# Example

Button Recipe

Purpose

Primary action.

Tokens

Surface Primary

Text On Primary

Radius Medium

Shadow Small

Transition Fast

Spacing Button Medium

Motion

Hover Lift

Pressed Scale

Focus Ring

Accessibility

Minimum height

44px mobile

Keyboard focus

Contrast AA

States

Default

Hover

Pressed

Focused

Disabled

Loading

Success

Error

Responsive

Small

Medium

Large

Full Width

Platform Support

Web

React Native

Flutter

SwiftUI

---

# Recipes Never Hardcode

Bad

Blue Background

18px Padding

14px Font

12px Radius

Good

Color Primary

Spacing Button Medium

Typography Button Medium

Radius Medium

Elevation Low

Transition Fast

Recipes consume Tokens.

---

# Variants

Recipes may have variants.

Example

Button

↓

Primary

Secondary

Outline

Ghost

Glass

Gradient

FAB

Split Button

Icon Button

Loading Button

Each variant is still one Recipe.

---

# Recipe States

Every Recipe documents

Loading

Skeleton

Hover

Pressed

Focused

Disabled

Empty

Success

Warning

Error

Offline

Reduced Motion

High Contrast

Dark Mode

---

# AI Rules

AI should never design a new Button.

AI should locate

Button Recipe

↓

Variant

↓

Theme

↓

Platform

↓

States

↓

Build

Reuse before creating.

---

# Recipe Registry

Every Recipe has

Recipe ID

Recipe Name

Version

Status

Owner

Related Components

Related Tokens

Related Patterns

Supported Platforms

Migration Guide

---

# Versioning

Recipes are versioned.

Major

Breaking changes

Minor

New variants

Patch

Documentation

