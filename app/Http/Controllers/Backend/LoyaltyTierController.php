<?php

namespace App\Http\Controllers\Backend;

use App\Actions\LoyaltyTiers\CreateLoyaltyTierAction;
use App\Actions\LoyaltyTiers\DeleteLoyaltyTierAction;
use App\Actions\LoyaltyTiers\UpdateLoyaltyTierAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreLoyaltyTierRequest;
use App\Http\Requests\Backend\UpdateLoyaltyTierRequest;
use App\Models\LoyaltyTier;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class LoyaltyTierController extends Controller
{
    /**
     * Display a listing of the loyalty tiers.
     */
    public function index(): Response
    {
        return Inertia::render('loyalty-tiers/Index', [
            'loyaltyTiers' => LoyaltyTier::query()->orderBy('points_required')->get(),
        ]);
    }

    /**
     * Show the form for creating a new loyalty tier.
     */
    public function create(): Response
    {
        return Inertia::render('loyalty-tiers/Create');
    }

    /**
     * Store a newly created loyalty tier.
     */
    public function store(StoreLoyaltyTierRequest $request, CreateLoyaltyTierAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the loyalty tier. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Loyalty tier created.')]);

        return to_route('loyalty-tiers.index');
    }

    /**
     * Show the form for editing the specified loyalty tier.
     */
    public function edit(LoyaltyTier $loyaltyTier): Response
    {
        return Inertia::render('loyalty-tiers/Edit', [
            'loyaltyTier' => $loyaltyTier,
        ]);
    }

    /**
     * Update the specified loyalty tier.
     */
    public function update(UpdateLoyaltyTierRequest $request, LoyaltyTier $loyaltyTier, UpdateLoyaltyTierAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($loyaltyTier, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the loyalty tier. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Loyalty tier updated.')]);

        return to_route('loyalty-tiers.index');
    }

    /**
     * Remove the specified loyalty tier.
     */
    public function destroy(LoyaltyTier $loyaltyTier, DeleteLoyaltyTierAction $action): RedirectResponse
    {
        try {
            $action->handle($loyaltyTier);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the loyalty tier. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Loyalty tier deleted.')]);

        return to_route('loyalty-tiers.index');
    }
}
