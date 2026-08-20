<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Products\CreateProductAction;
use App\Actions\Products\DeleteProductAction;
use App\Actions\Products\UpdateProductAction;
use App\Enums\TaxType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreProductRequest;
use App\Http\Requests\Backend\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    /**
     * Display a listing of the products.
     */
    public function index(): Response
    {
        return Inertia::render('products/Index', [
            'products' => $this->productService->list(),
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
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request, CreateProductAction $action): RedirectResponse
    {
        $data = $request->safe()->except('thumbnail');
        $data['status'] = $request->boolean('status');
        $data['free_shipping'] = $request->boolean('free_shipping');
        $data['refundable'] = $request->boolean('refundable');
        $data['featured'] = $request->boolean('featured');

        try {
            $action->handle($data, $request->file('thumbnail'));
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
            'categories' => $this->categoryOptions(),
            'brands' => $this->brandOptions(),
            'taxTypes' => $this->taxTypeOptions(),
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product, UpdateProductAction $action): RedirectResponse
    {
        $data = $request->safe()->except('thumbnail');
        $data['status'] = $request->boolean('status');
        $data['free_shipping'] = $request->boolean('free_shipping');
        $data['refundable'] = $request->boolean('refundable');
        $data['featured'] = $request->boolean('featured');

        try {
            $action->handle($product, $data, $request->file('thumbnail'));
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
}
