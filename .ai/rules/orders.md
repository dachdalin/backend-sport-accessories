---
paths:
  - 'app/Services/OrderService.php,app/Http/Controllers/Backend/OrderController.php,resources/js/pages/orders/**'
---

# Orders

## Orders index gained pagination + filters (2026-08-28)
OrderService::list() changed signature: now `list(array $filters = []): LengthAwarePaginator` (was `list(): Collection`) — follows the exact ProductService::list() pattern (`->when()` per filter, `->latest()->paginate(15)->withQueryString()`). Filters: order_status (exact), payment_status (exact), search (LIKE across order_number/customer_name/customer_email, OR'd in one where-closure). OrderController::index(Request $request) passes `$request->only(['order_status','payment_status','search'])` through as both the list() arg and a `filters` prop, plus `orderStatuses`/`paymentStatuses` select options (reused the controller's existing private option methods). orders/Index.vue now expects `orders` as `Paginated<Order>` (`{data,links}`, not a plain array) and mirrors products/Index.vue's Select-filter-with-immediate-apply pattern, plus a debounced (300ms, plain setTimeout — no debounce lib in this repo) search Input. If another admin list needs a text search box, this is the first search-input reference (products/Index.vue only has Select filters, no search box). Don't reintroduce the old unpaginated Collection return — every other caller of OrderService only uses its other methods (generateOrderNumber, calculateOrderAmount, resolveItems, etc.), not list().
