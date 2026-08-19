---
paths:
  - 'resources/js/pages/**'
---

# Pages

## defineOptions layout breadcrumbs can't reference props directly
`defineOptions({ layout: { breadcrumbs: [...] } })` is hoisted out of setup(), so referencing `props.foo` (from `defineProps`) inside it fails to build: "cannot reference locally declared variables". For breadcrumbs/layout data that depend on the page's own props (e.g. an edit page needing the record id in a route), use the callback form instead: `defineOptions({ layout: (pageProps) => ({ breadcrumbs: [...] }) })`, where `pageProps` is the Inertia page props object (same shape as what `Inertia::render()` sent), not the local `props` variable.
