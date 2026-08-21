<?php

namespace App\Http\Controllers\Backend;

use App\Actions\MostDemandeds\CreateMostDemandedAction;
use App\Actions\MostDemandeds\DeleteMostDemandedAction;
use App\Actions\MostDemandeds\UpdateMostDemandedAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreMostDemandedRequest;
use App\Http\Requests\Backend\UpdateMostDemandedRequest;
use App\Models\MostDemanded;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class MostDemandedController extends Controller
{
    /**
     * Display a listing of the most demanded products.
     */
    public function index(): Response
    {
        return Inertia::render('most-demandeds/Index', [
            'mostDemandeds' => MostDemanded::query()
                ->with('product:id,name')
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new most demanded entry.
     */
    public function create(): Response
    {
        return Inertia::render('most-demandeds/Create', [
            'products' => $this->productOptions(),
        ]);
    }

    /**
     * Store a newly created most demanded entry.
     */
    public function store(StoreMostDemandedRequest $request, CreateMostDemandedAction $action): RedirectResponse
    {
        $data = $request->safe()->except('banner');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data, $request->file('banner'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the entry. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Most demanded entry created.')]);

        return to_route('most-demandeds.index');
    }

    /**
     * Show the form for editing the specified most demanded entry.
     */
    public function edit(MostDemanded $mostDemanded): Response
    {
        return Inertia::render('most-demandeds/Edit', [
            'mostDemanded' => $mostDemanded,
            'products' => $this->productOptions(),
        ]);
    }

    /**
     * Update the specified most demanded entry.
     */
    public function update(UpdateMostDemandedRequest $request, MostDemanded $mostDemanded, UpdateMostDemandedAction $action): RedirectResponse
    {
        $data = $request->safe()->except('banner');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($mostDemanded, $data, $request->file('banner'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the entry. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Most demanded entry updated.')]);

        return to_route('most-demandeds.index');
    }

    /**
     * Remove the specified most demanded entry.
     */
    public function destroy(MostDemanded $mostDemanded, DeleteMostDemandedAction $action): RedirectResponse
    {
        try {
            $action->handle($mostDemanded);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the entry. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Most demanded entry deleted.')]);

        return to_route('most-demandeds.index');
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
}
