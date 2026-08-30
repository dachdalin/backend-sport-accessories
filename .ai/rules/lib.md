---
paths:
  - 'resources/js/pages/flash-deals/**,resources/js/components/ColorSwatchInput.vue,resources/js/components/FlashDealPreview.vue,resources/js/lib/colors.ts'
---

# Lib

## Flash deal Create/Edit redesigned as Card-sectioned forms (2026-08-30)
Rebuilt to follow the ProductController Create/Edit pattern: `grid gap-6 lg:grid-cols-3 lg:items-start` with a `lg:col-span-2` main column (Details/Schedule/Banner/Appearance/Deal products Cards, each `CardHeader` with a lucide icon + `CardTitle`) and a sidebar column (Preview/Visibility/Publish, Publish card `lg:sticky lg:top-6`). Visibility checkboxes use the `has-[[data-state=checked]]:border-primary` toggle-row style from ProductController, not plain inline checkboxes. Repeater rows use `border-input` (not the older `border-sidebar-border/70`) with `sm:grid-cols-[2fr_1fr_1fr_auto]` for mobile stacking.

Banner upload now uses `ImageDropzone` (drag/drop + preview) instead of a bare `<input type=file>`; Edit passes `:initial-previews="[\`/storage/${flashDeal.banner}\`]"`.

New reusable pieces, not flash-deal-specific — check before rebuilding elsewhere:
- `resources/js/lib/colors.ts`: `expandHex()` (normalizes `#rgb`/`#rrggbb`) and `contrastRatio()` (WCAG contrast, 1–21).
- `resources/js/components/ColorSwatchInput.vue`: labeled hex text input + native color-picker swatch, two-way `v-model`, still renders the real named `<input>` for Inertia Form serialization.
- `resources/js/components/FlashDealPreview.vue`: signature live preview — renders the actual badge (title on background/text color) plus a computed schedule label ("Starts in N days", "Live now, ends in N days") and a low-contrast warning (ratio < 3) via `contrastRatio()`. Takes plain string props (title/startDate/endDate/backgroundColor/textColor); the page must keep those fields in `ref()`s with matching `v-model` (in addition to `name=`) to drive it live — title/start_date/end_date/background_color/text_color weren't reactive before this change.

Verified: `npm run build`, `npm run types:check`, eslint/prettier, `php artisan test tests/Feature/FlashDealControllerTest.php` (12/12) all pass. Visually checked Create.vue live in-browser (title/date/color inputs drive the preview and contrast warning correctly) — did not get a real mobile-width screenshot since `resize_window` didn't take effect in that session; responsiveness rests on the same Tailwind breakpoints as ProductController's already-shipped form.
