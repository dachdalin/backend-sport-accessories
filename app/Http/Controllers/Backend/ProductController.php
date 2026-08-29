<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Products\CreateProductAction;
use App\Actions\Products\DeleteProductAction;
use App\Actions\Products\UpdateProductAction;
use App\Enums\TaxType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreProductRequest;
use App\Http\Requests\Backend\UpdateProductRequest;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Material;
use App\Models\Product;
use App\Models\Size;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    /**
     * Display a listing of the products.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['category_id', 'brand_id']);

        return Inertia::render('products/Index', [
            'products' => $this->productService->list($filters),
            'categories' => $this->categoryOptions(),
            'brands' => $this->brandOptions(),
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): Response
    {
        return Inertia::render('products/Create', [
            'categories' => $this->categoryOptions(),
            'brands' => $this->brandOptions(),
            'taxTypes' => $this->taxTypeOptions(),
            'colors' => $this->colorOptions(),
            'sizes' => $this->sizeOptions(),
            'materials' => $this->materialOptions(),
            'attributeOptions' => $this->attributeOptions(),
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request, CreateProductAction $action): RedirectResponse
    {
        $data = $request->safe()->except('thumbnail', 'images', 'variants', 'attributes');
        $data['status'] = $request->boolean('status');
        $data['free_shipping'] = $request->boolean('free_shipping');
        $data['refundable'] = $request->boolean('refundable');
        $data['featured'] = $request->boolean('featured');

        $variants = $request->validated('variants') ?? [];
        $attributes = $request->validated('attributes') ?? [];

        try {
            $action->handle($data, $request->file('thumbnail'), $request->file('images'), $variants, $attributes);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the product. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Product created.')]);

        return to_route('products.index');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): Response
    {
        return Inertia::render('products/Edit', [
            'product' => $product,
            'images' => $product->images,
            'variants' => $product->variants,
            'productAttributeIds' => $product->attributes()->pluck('attributes.id'),
            'categories' => $this->categoryOptions(),
            'brands' => $this->brandOptions(),
            'taxTypes' => $this->taxTypeOptions(),
            'colors' => $this->colorOptions(),
            'sizes' => $this->sizeOptions(),
            'materials' => $this->materialOptions(),
            'attributeOptions' => $this->attributeOptions(),
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product, UpdateProductAction $action): RedirectResponse
    {
        $data = $request->safe()->except('thumbnail', 'variants', 'attributes');
        $data['status'] = $request->boolean('status');
        $data['free_shipping'] = $request->boolean('free_shipping');
        $data['refundable'] = $request->boolean('refundable');
        $data['featured'] = $request->boolean('featured');

        $variants = $request->validated('variants') ?? [];
        $attributes = $request->validated('attributes') ?? [];

        try {
            $action->handle($product, $data, $request->file('thumbnail'), $variants, $attributes);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the product. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Product updated.')]);

        return to_route('products.index');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product, DeleteProductAction $action): RedirectResponse
    {
        try {
            $action->handle($product);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the product. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Product deleted.')]);

        return to_route('products.index');
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function categoryOptions(): array
    {
        return Category::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Category $category) => ['value' => $category->id, 'label' => $category->name])
            ->all();
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function brandOptions(): array
    {
        return Brand::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Brand $brand) => ['value' => $brand->id, 'label' => $brand->name])
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function taxTypeOptions(): array
    {
        return array_map(
            fn (TaxType $case) => ['value' => $case->value, 'label' => $case->label()],
            TaxType::cases(),
        );
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function colorOptions(): array
    {
        return Color::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn (Color $color) => ['value' => $color->id, 'label' => $color->name, 'code' => $color->code])
            ->all();
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function sizeOptions(): array
    {
        return Size::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Size $size) => ['value' => $size->id, 'label' => $size->name])
            ->all();
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function materialOptions(): array
    {
        return Material::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Material $material) => ['value' => $material->id, 'label' => $material->name])
            ->all();
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function attributeOptions(): array
    {
        return Attribute::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Attribute $attribute) => ['value' => $attribute->id, 'label' => $attribute->name])
            ->all();
    }
}
