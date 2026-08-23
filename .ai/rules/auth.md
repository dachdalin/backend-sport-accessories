---
paths:
  - 'resources/js/layouts/auth/**'
---

# Auth

## Auth layout re-themes shadcn tokens locally — always redeclare `color` too
AuthSimpleLayout.vue gives the sign-in flow its own fixed "gear-tag" skin by redefining shadcn's CSS custom properties (--foreground, --primary, --input, etc.) on the .auth-shell wrapper, deliberately ignoring the app-wide light/dark toggle so every staff sign-in looks the same.

Trap: redefining --foreground alone does NOT recolor inherited text (Label, plain body text) because `color` is set once on <body> via `text-foreground` and inherits by resolved value, not by re-evaluating var() at each descendant. You must also redeclare `color: var(--ink)` directly on .auth-shell, or descendant text silently keeps the ambient (possibly dark-mode) color. Verified by toggling `document.documentElement.classList.add('dark')` and checking label contrast.

Any future auth-page component (elements rendered via the layout's default slot) automatically inherits the tag palette — no per-page CSS needed. Don't add dark: variants inside these auth pages; the shell is intentionally theme-locked.
