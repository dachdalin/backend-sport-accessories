<?php

namespace App\Http\Controllers\Backend;

use App\Actions\FlashDeals\CreateFlashDealAction;
use App\Actions\FlashDeals\DeleteFlashDealAction;
use App\Actions\FlashDeals\UpdateFlashDealAction;
use App\Enums\TaxType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreFlashDealRequest;
use App\Http\Requests\Backend\UpdateFlashDealRequest;
use App\Models\FlashDeal;
use App\Models\Product;
use App\Services\FlashDealService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class FlashDealController extends Controller
{
    public function __construct(private readonly FlashDealService $flashDealService) {}

    /**
     * Display a listing of the flash deals.
     */
    public function index(): Response
    {
        return Inertia::render('flash-deals/Index', [
            'flashDeals' => $this->flashDealService->list(),
        ]);
    }

    /**
     * Show the form for creating a new flash deal.
     */
    public function create(): Response
    {
        return Inertia::render('flash-deals/Create', [
            'products' => $this->productOptions(),
            'discountTypes' => $this->discountTypeOptions(),
        ]);
    }

    /**
     * Store a newly created flash deal.
     */
    public function store(StoreFlashDealRequest $request, CreateFlashDealAction $action): RedirectResponse
    {
        $data = $request->safe()->except('banner', 'items');
        $data['status'] = $request->boolean('status');
        $data['featured'] = $request->boolean('featured');
        $items = $request->validated('items');

        try {
            $action->handle($data, $items, $request->file('banner'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the flash deal. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Flash deal created.')]);

        return to_route('flash-deals.index');
    }

    /**
     * Show the form for editing the specified flash deal.
     */
    public function edit(FlashDeal $flashDeal): Response
    {
        return Inertia::render('flash-deals/Edit', [
            'flashDeal' => $flashDeal->load('items'),
            'products' => $this->productOptions(),
            'discountTypes' => $this->discountTypeOptions(),
        ]);
    }

    /**
     * Update the specified flash deal.
     */
    public function update(UpdateFlashDealRequest $request, FlashDeal $flashDeal, UpdateFlashDealAction $action): RedirectResponse
    {
        $data = $request->safe()->except('banner', 'items');
        $data['status'] = $request->boolean('status');
        $data['featured'] = $request->boolean('featured');
        $items = $request->validated('items');

        try {
            $action->handle($flashDeal, $data, $items, $request->file('banner'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the flash deal. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Flash deal updated.')]);

        return to_route('flash-deals.index');
    }

    /**
     * Remove the specified flash deal.
     */
    public function destroy(FlashDeal $flashDeal, DeleteFlashDealAction $action): RedirectResponse
    {
        try {
            $action->handle($flashDeal);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the flash deal. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Flash deal deleted.')]);

        return to_route('flash-deals.index');
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function productOptions(): array
    {
        return Product::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Product $product) => ['value' => $product->id, 'label' => $product->name])
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function discountTypeOptions(): array
    {
        return array_map(
            fn (TaxType $case) => ['value' => $case->value, 'label' => $case->label()],
            TaxType::cases(),
        );
    }
}
