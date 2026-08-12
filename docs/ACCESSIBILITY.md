# Accessibility checklist

> Phase 12 public-site baseline. Treat as a living checklist for marketing pages and the client portal shell.

## Done

- [x] Skip link to `#main-content` on the public layout
- [x] Semantic landmarks: header / `main` / footer
- [x] Focusable nav and CTA controls (native links/buttons)
- [x] Toast host uses `aria-live="polite"` for realtime notices
- [x] Form labels on Livewire contact / quote / auth flows (existing patterns)
- [x] Theme toggle does not rely on colour alone for meaning

## Targets

| Area | Target |
|------|--------|
| Keyboard | All primary actions reachable without a pointer |
| Contrast | Body text ≥ WCAG AA against surface backgrounds |
| Focus | Visible focus ring on interactive elements |
| Motion | Prefer CSS that respects `prefers-reduced-motion` for large animations (ongoing) |
| Images | Meaningful `alt` on content images; decorative media empty/`aria-hidden` |

## Manual pass (before release)

1. Tab through home → services → contact → quote without a mouse
2. Zoom to 200% — no critical overflow on home and legal pages
3. Screen reader spot-check: skip link, main heading, FAQ accordion, form errors
4. Dark theme: confirm contrast on cards, footer legal links, toasts

## Out of scope (later)

- Full WCAG audit tooling in CI
- Automated axe scans on every PR
- Captioned video / complex ARIA widgets beyond current FAQs
