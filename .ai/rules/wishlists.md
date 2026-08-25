---
paths:
  - 'app/Http/Controllers/Backend/WishlistController.php,app/Services/WishlistService.php,resources/js/pages/wishlists/**'
---

# Wishlists

## Wishlists index is paginated via WishlistService
WishlistService::list() returns ->paginate(15)->withQueryString() (LengthAwarePaginator), not ->get(). wishlists/Index.vue expects `wishlists` prop as `{ data: Wishlist[], links: {url,label,active}[] }` and renders links same as gift-cards/Index.vue, feature-deals/Index.vue. Test asserts via `has('wishlists.data', n)`.
