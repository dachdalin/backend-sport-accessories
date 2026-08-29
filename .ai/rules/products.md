---
paths:
  - 'app/Http/Controllers/Backend/ProductController.php,app/Http/Requests/Backend/*Product*.php,app/Actions/Products/**,app/Models/Product.php,app/Models/ProductVariant.php,resources/js/pages/products/**'
---

# Products

## Product variants (color/size/material) and attributes
Products carry stock-per-combination variants and a tag-style attribute list, added 2026-08-29:
- `product_variants` table: product_id, nullable color_id/size_id/material_id, sku (unique), extra_price, stock. Product::variants() hasMany. Each variant needs at least one of color/size/material (enforced in Store/UpdateProductRequest::after(), not a DB constraint) and combos must be distinct per product — also app-level, checked in after().
- `attribute_product` pivot (plain many-to-many, no extra columns): Product::attributes() belongsToMany(Attribute::class). In Product.php, App\Models\Attribute must be imported aliased (e.g. `Attribute as ProductAttribute`) because Illuminate\Database\Eloquent\Casts\Attribute is already imported there for finalPrice().
- Create/Update flow follows the stock-clearance-setups items pattern: `variants[]`/`attributes[]` submitted alongside the main product form (same `<Form>`, array field names `variants[${i}][color_id]` etc.), extracted in the controller via `$request->validated('variants')`/`('attributes')` and passed to CreateProductAction/UpdateProductAction. Update always does variants()->delete() then createMany() (full replace, no diffing) and attributes()->sync() (empty array detaches all). See [[stock-clearance-setups]].
