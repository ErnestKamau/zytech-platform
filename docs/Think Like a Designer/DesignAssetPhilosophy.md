# =============================================================================
# Design Assets Philosophy
# =============================================================================

Our design system must never depend on real production assets during
development.

Instead every visual component must support placeholder content from day one.

This allows developers, designers and AI agents to build complete pages
before marketing content, project photography and videos are available.

Every component should gracefully handle

• Placeholder images
• Placeholder videos
• Placeholder backgrounds
• Missing avatars
• Missing project covers
• Missing logos
• Missing documents

Components should never break because assets are unavailable.

---

# Placeholder Standards

Every visual component should provide sensible defaults.

Project Card

↓

Placeholder Project Image

Project Gallery

↓

Placeholder Gallery Images

Project Hero

↓

Placeholder Hero Background

Video Section

↓

Placeholder Thumbnail

↓

Placeholder Video

Client Logo

↓

Placeholder Company Logo

Profile

↓

Placeholder Avatar

Knowledge Centre

↓

Placeholder Cover Image

Services

↓

Placeholder Illustration

This enables parallel development.

Developers build.

Marketing later replaces assets.

Nothing changes structurally.
