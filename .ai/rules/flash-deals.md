---
paths:
  - 'app/Http/Controllers/Backend/FlashDealController.php,app/Services/FlashDealService.php,resources/js/pages/flash-deals/**'
---

# Flash Deals

## FlashDeal index now paginated (2026-08-23)
FlashDealService::list() paginates(15)->withQueryString() now, matching tags/brands/deal-of-the-days convention (was ->get() with no pagination before). Index.vue flashDeals prop is Paginated<FlashDeal> (data + links), template loops flashDeals.data and renders the standard links pagination block at the bottom (copied from deal-of-the-days/Index.vue). FeatureDealController still lacks pagination — same fix applies there if revisited.
