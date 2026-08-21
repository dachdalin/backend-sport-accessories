<?php

namespace App\Http\Controllers\Backend;

use App\Actions\DealOfTheDays\CreateDealOfTheDayAction;
use App\Actions\DealOfTheDays\DeleteDealOfTheDayAction;
use App\Actions\DealOfTheDays\UpdateDealOfTheDayAction;
use App\Enums\TaxType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreDealOfTheDayRequest;
use App\Http\Requests\Backend\UpdateDealOfTheDayRequest;
use App\Models\DealOfTheDay;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class DealOfTheDayController extends Controller
{
    /**
     * Display a listing of the deals.
     */
    public function index(): Response
    {
        return Inertia::render('deal-of-the-days/Index', [
            'deals' => DealOfTheDay::query()
                ->with('product:id,name')
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new deal.
     */
    public function create(): Response
    {
        return Inertia::render('deal-of-the-days/Create', [
            'products' => $this->productOptions(),
            'discountTypes' => $this->discountTypeOptions(),
        ]);
    }

    /**
     * Store a newly created deal.
     */
    public function store(StoreDealOfTheDayRequest $request, CreateDealOfTheDayAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the deal. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Deal of the day created.')]);

        return to_route('deal-of-the-days.index');
    }

    /**
     * Show the form for editing the specified deal.
     */
    public function edit(DealOfTheDay $dealOfTheDay): Response
    {
        return Inertia::render('deal-of-the-days/Edit', [
            'deal' => $dealOfTheDay,
            'products' => $this->productOptions(),
            'discountTypes' => $this->discountTypeOptions(),
        ]);
    }

    /**
     * Update the specified deal.
     */
    public function update(UpdateDealOfTheDayRequest $request, DealOfTheDay $dealOfTheDay, UpdateDealOfTheDayAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($dealOfTheDay, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the deal. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Deal of the day updated.')]);

        return to_route('deal-of-the-days.index');
    }

    /**
     * Remove the specified deal.
     */
    public function destroy(DealOfTheDay $dealOfTheDay, DeleteDealOfTheDayAction $action): RedirectResponse
    {
        try {
            $action->handle($dealOfTheDay);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the deal. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Deal of the day deleted.')]);

        return to_route('deal-of-the-days.index');
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
