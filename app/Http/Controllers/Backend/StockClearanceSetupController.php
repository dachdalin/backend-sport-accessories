<?php

namespace App\Http\Controllers\Backend;

use App\Actions\StockClearanceSetups\CreateStockClearanceSetupAction;
use App\Actions\StockClearanceSetups\DeleteStockClearanceSetupAction;
use App\Actions\StockClearanceSetups\UpdateStockClearanceSetupAction;
use App\Enums\OfferActiveTime;
use App\Enums\TaxType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreStockClearanceSetupRequest;
use App\Http\Requests\Backend\UpdateStockClearanceSetupRequest;
use App\Models\Product;
use App\Models\StockClearanceSetup;
use App\Services\StockClearanceSetupService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class StockClearanceSetupController extends Controller
{
    public function __construct(private readonly StockClearanceSetupService $stockClearanceSetupService) {}

    /**
     * Display a listing of the stock clearance setups.
     */
    public function index(): Response
    {
        return Inertia::render('stock-clearance-setups/Index', [
            'stockClearanceSetups' => $this->stockClearanceSetupService->list(),
        ]);
    }

    /**
     * Show the form for creating a new stock clearance setup.
     */
    public function create(): Response
    {
        return Inertia::render('stock-clearance-setups/Create', [
            'products' => $this->productOptions(),
            'discountTypes' => $this->discountTypeOptions(),
            'offerActiveTimes' => $this->offerActiveTimeOptions(),
        ]);
    }

    /**
     * Store a newly created stock clearance setup.
     */
    public function store(StoreStockClearanceSetupRequest $request, CreateStockClearanceSetupAction $action): RedirectResponse
    {
        $data = $request->safe()->except('items');
        $data['show_in_homepage'] = $request->boolean('show_in_homepage');
        $data['show_in_homepage_once'] = $request->boolean('show_in_homepage_once');
        $data['show_in_shop'] = $request->boolean('show_in_shop');
        $data['is_active'] = $request->boolean('is_active');
        $items = $request->validated('items');

        try {
            $action->handle($data, $items);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the stock clearance setup. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Stock clearance setup created.')]);

        return to_route('stock-clearance-setups.index');
    }

    /**
     * Show the form for editing the specified stock clearance setup.
     */
    public function edit(StockClearanceSetup $stockClearanceSetup): Response
    {
        return Inertia::render('stock-clearance-setups/Edit', [
            'stockClearanceSetup' => $stockClearanceSetup->load('items'),
            'products' => $this->productOptions(),
            'discountTypes' => $this->discountTypeOptions(),
            'offerActiveTimes' => $this->offerActiveTimeOptions(),
        ]);
    }

    /**
     * Update the specified stock clearance setup.
     */
    public function update(UpdateStockClearanceSetupRequest $request, StockClearanceSetup $stockClearanceSetup, UpdateStockClearanceSetupAction $action): RedirectResponse
    {
        $data = $request->safe()->except('items');
        $data['show_in_homepage'] = $request->boolean('show_in_homepage');
        $data['show_in_homepage_once'] = $request->boolean('show_in_homepage_once');
        $data['show_in_shop'] = $request->boolean('show_in_shop');
        $data['is_active'] = $request->boolean('is_active');
        $items = $request->validated('items');

        try {
            $action->handle($stockClearanceSetup, $data, $items);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the stock clearance setup. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Stock clearance setup updated.')]);

        return to_route('stock-clearance-setups.index');
    }

    /**
     * Remove the specified stock clearance setup.
     */
    public function destroy(StockClearanceSetup $stockClearanceSetup, DeleteStockClearanceSetupAction $action): RedirectResponse
    {
        try {
            $action->handle($stockClearanceSetup);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the stock clearance setup. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Stock clearance setup deleted.')]);

        return to_route('stock-clearance-setups.index');
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

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function offerActiveTimeOptions(): array
    {
        return array_map(
            fn (OfferActiveTime $case) => ['value' => $case->value, 'label' => $case->label()],
            OfferActiveTime::cases(),
        );
    }
}
