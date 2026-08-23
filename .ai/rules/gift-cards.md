---
paths:
  - 'app/Http/Controllers/Backend/GiftCardController.php,resources/js/pages/gift-cards/**'
---

# Gift Cards

## Gift cards index is paginated, like materials/users/attributes
GiftCardController@index uses ->paginate(15)->withQueryString(), not ->get(). gift-cards/Index.vue expects `giftCards` prop as `{ data: GiftCard[], links: {url,label,active}[] }` and renders links same as materials/Index.vue. Test asserts via `has('giftCards.data', n)`.
