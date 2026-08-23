---
paths:
  - 'app/Http/Controllers/Backend/AttributeController.php,resources/js/pages/attributes/**'
---

# Attributes

## Attributes index is paginated, like users
AttributeController@index uses ->paginate(15)->withQueryString() (added 2026-08-23), not ->get(). attributes/Index.vue expects `attributes` prop as `{ data: Attribute[], links: {url,label,active}[] }`. Test asserts via `has('attributes.data', n)` / `where('attributes.data.0.id', ...)`, not top-level `attributes`. See also [[users]] for the same pattern.
