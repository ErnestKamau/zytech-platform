# Zytech Platform Engineering Constitution
## 99_AGENT_RULES.md

> Version 1.0

---

# Table of Contents

1. Purpose
2. Engineering Philosophy
3. Core Principles
4. Golden Rules
5. Architecture Rules
6. Domain Driven Design Rules
7. Laravel Rules
8. Livewire Rules
9. Blade Rules
10. Alpine.js Rules
11. Filament Rules
12. PostgreSQL Rules
13. Redis Rules
14. Queue Rules
15. Laravel Reverb Rules
16. Media Rules
17. API Rules
18. Security Rules
19. Performance Rules
20. UI/UX Rules
21. Accessibility Rules
22. Testing Rules
23. Git Workflow
24. Documentation Rules
25. AI Anti-Patterns
26. AI Completion Checklist
27. Definition of Done
28. Future Expansion Rules

---

# Purpose

This document defines the engineering standards for the Zytech Platform.

Every developer, AI coding assistant, and contributor must follow these rules.

The goals are:

- Maintain consistency
- Produce enterprise-quality software
- Minimize technical debt
- Ensure maintainability
- Maximize performance
- Prioritize security
- Encourage modularity
- Enable future scalability

If any generated code conflicts with this document, this document takes precedence.

---

# Engineering Philosophy

The platform exists to solve business problems, not to demonstrate technical cleverness.

Every engineering decision should optimize for:

- Simplicity
- Readability
- Maintainability
- Performance
- Security
- Scalability
- Testability
- Reusability

Always choose the solution that is easiest to understand six months later.

```

---

# Core Principles

```
Business First

↓

Architecture Before Features

↓

Security By Default

↓

Performance By Design

↓

Reuse Before Rewrite

↓

Events Over Tight Coupling

↓

Composition Over Inheritance

↓

Services Over Fat Controllers

↓

Queues Over Blocking Requests

↓

Observability Everywhere
```

---

# Golden Rules

## UUID First

Never expose numeric IDs.

Use UUID route model binding everywhere.

---

## Thin Controllers

Controllers orchestrate.

Nothing more.

---

## Thin Livewire Components

UI logic only.

Business logic belongs elsewhere.

---

## Thin Filament Resources

Resources define UI.

Never business rules.

---

## Services Own Business Logic

Complex business rules belong inside Services.

---

## Actions Perform One Task

Every Action performs one job.

No side effects.

---

## Events Broadcast Business Changes

Every significant business event dispatches a Laravel Event.

---

## Queue Heavy Operations

Never process expensive work during HTTP requests.

---

## Cache Expensive Reads

Redis first.

Never cache transactional writes.

---

## Policies Everywhere

Every business resource must have a Policy.

Never authorize in controllers.

---

## Activity Logging

Every important business action must be auditable.

---

## Mobile First

Every UI must work on mobile.

---

## Accessibility Matters

Every page must meet WCAG AA.

---

## Documentation Is Mandatory

New features require documentation updates.

---

# Architecture Rules

(continued...)
