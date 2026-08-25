---
paths:
  - 'app/Http/Controllers/Backend/FeatureDealController.php,resources/js/pages/feature-deals/**'
---

# Feature Deals

## Feature deals index is paginated, like gift-cards/materials
FeatureDealController@index uses ->paginate(15)->withQueryString(), not ->get(). feature-deals/Index.vue expects `featureDeals` prop as `{ data: FeatureDeal[], links: {url,label,active}[] }` and renders links same as gift-cards/Index.vue. Test asserts via `has('featureDeals.data', n)`.
