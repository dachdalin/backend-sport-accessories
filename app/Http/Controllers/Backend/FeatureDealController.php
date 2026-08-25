<?php

namespace App\Http\Controllers\Backend;

use App\Actions\FeatureDeals\CreateFeatureDealAction;
use App\Actions\FeatureDeals\DeleteFeatureDealAction;
use App\Actions\FeatureDeals\UpdateFeatureDealAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreFeatureDealRequest;
use App\Http\Requests\Backend\UpdateFeatureDealRequest;
use App\Models\FeatureDeal;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class FeatureDealController extends Controller
{
    /**
     * Display a listing of the feature deals.
     */
    public function index(): Response
    {
        return Inertia::render('feature-deals/Index', [
            'featureDeals' => FeatureDeal::query()->latest()->paginate(15)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new feature deal.
     */
    public function create(): Response
    {
        return Inertia::render('feature-deals/Create');
    }

    /**
     * Store a newly created feature deal.
     */
    public function store(StoreFeatureDealRequest $request, CreateFeatureDealAction $action): RedirectResponse
    {
        $data = $request->safe()->except('photo');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data, $request->file('photo'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the feature deal. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Feature deal created.')]);

        return to_route('feature-deals.index');
    }

    /**
     * Show the form for editing the specified feature deal.
     */
    public function edit(FeatureDeal $featureDeal): Response
    {
        return Inertia::render('feature-deals/Edit', [
            'featureDeal' => $featureDeal,
        ]);
    }

    /**
     * Update the specified feature deal.
     */
    public function update(UpdateFeatureDealRequest $request, FeatureDeal $featureDeal, UpdateFeatureDealAction $action): RedirectResponse
    {
        $data = $request->safe()->except('photo');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($featureDeal, $data, $request->file('photo'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the feature deal. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Feature deal updated.')]);

        return to_route('feature-deals.index');
    }

    /**
     * Remove the specified feature deal.
     */
    public function destroy(FeatureDeal $featureDeal, DeleteFeatureDealAction $action): RedirectResponse
    {
        try {
            $action->handle($featureDeal);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the feature deal. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Feature deal deleted.')]);

        return to_route('feature-deals.index');
    }
}
