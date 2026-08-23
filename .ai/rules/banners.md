---
paths:
  - 'app/Http/Controllers/Backend/BannerController.php,resources/js/pages/banners/**'
---

# Banners

## Banners index is paginated, like gift-cards/materials/users/attributes
BannerController@index uses ->orderBy('sort_order')->latest()->paginate(15)->withQueryString(), not ->get(). banners/Index.vue expects `banners` prop as `{ data: Banner[], links: {url,label,active}[] }` and renders links the same as gift-cards/Index.vue. Test asserts via `has('banners.data', n)`. See [[gift-cards]] for the same pattern.
