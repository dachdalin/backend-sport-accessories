---
paths:
  - 'app/Services/StockClearanceSetupService.php,app/Http/Controllers/Backend/StockClearanceSetupController.php,resources/js/pages/stock-clearance-setups/**'
---

# Stock Clearance Setups

## Stock clearance setups index is paginated, like users/attributes/materials
StockClearanceSetupService::list() now returns LengthAwarePaginator via ->paginate(15)->withQueryString() (added 2026-08-23), not a Collection from ->get(). stock-clearance-setups/Index.vue expects `stockClearanceSetups` prop as `{ data: StockClearanceSetup[], links: {url,label,active}[] }`. See [[materials]] for the same pattern.
