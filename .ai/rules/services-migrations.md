---
paths:
  - 'app/Services/DashboardService.php,database/migrations/**'
---

# Services Migrations

## Dashboard stats() is one aggregated query, not three — keep it that way
DashboardService::stats() used to run 3 separate queries against `orders` (sum, count, distinct-count). Replaced with a single `toBase()->selectRaw('count(*) as orders_count, sum(order_amount) as revenue, count(distinct customer_email) as customers')->first()` — one round trip instead of three. Verified via DB::enableQueryLog().

`orders.created_at` had no index despite every dashboard query (stats/salesChart/topProducts) filtering on it via whereBetween — added via a new migration (2026_08_23_133326_add_created_at_index_to_orders_table), not by editing the original create_orders_table migration (already-deployed migrations are immutable per [[migrations]]).

salesChart() still pulls raw order rows into PHP to bucket by hour/day/month rather than aggregating in SQL — left as-is deliberately: this app runs MySQL in prod but SQLite in tests (phpunit.xml), and MySQL/SQLite date-truncation functions (DATE_FORMAT vs strftime) aren't portable, so a driver-specific rewrite would add real risk for uncertain gain at this app's scale. Revisit only if a real slow-query symptom shows up on the `this_year` bucket.
