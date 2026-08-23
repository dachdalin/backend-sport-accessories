---
paths:
  - 'app/Http/Controllers/Backend/MaterialController.php,resources/js/pages/materials/**'
---

# Materials

## Materials index is paginated, like users/attributes
MaterialController@index uses ->paginate(15)->withQueryString() (added 2026-08-23), not ->get(). materials/Index.vue expects `materials` prop as `{ data: Material[], links: {url,label,active}[] }`. Test asserts via `has('materials.data', n)` / `where('materials.data.0.id', ...)`. See [[attributes]] for the same pattern.
