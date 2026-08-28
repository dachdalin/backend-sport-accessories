---
paths:
  - 'resources/js/pages/**/Index.vue,resources/js/components/Pagination.vue'
---

# Js Components

## Use the shared Pagination component for every paginated Index list
Redesigned 2026-08-28: replaced the ~20-line inline `v-if="X.links.length > 3"` block duplicated across all 26 paginated Index.vue pages with `resources/js/components/Pagination.vue`. New page usage: `<Pagination :meta="products" label="products" />`.

- The page's local `Paginated<T>` type/interface must include `from`, `to`, `total`, `current_page`, `last_page` (all present on the wire already — Laravel's default `->paginate()->toArray()` — just not previously typed) alongside the existing `data`/`links`.
- `label` is the plural noun for the summary line ("Showing 1–15 of 18 products") — match the page's own vocabulary (e.g. "entries" for most-demandeds, whose own copy says "Add entry").
- The component self-hides when there's only one page (`links.length <= 3`), matching every page's old gating — don't wrap it in an extra `v-if` at the call site.
- Signature look: previous/next chevrons + page-number pills live inside ONE connected bordered track (not floating independent pills) — matches this app's everywhere-a-bordered-card visual grammar. Active page uses `bg-primary`/`text-primary-foreground` (monochrome) — never the sidebar's orange `--sidebar-primary`, which is scoped to sidebar branding only.
- Mobile (`<sm`): the numbered pills hide and a single "Page X of Y" span takes their place between the chevrons, to avoid overflow on the admin's narrow content area.
