<?php

namespace App\Http\Controllers\Backend;

use App\Actions\DeliveryZones\CreateDeliveryZoneAction;
use App\Actions\DeliveryZones\DeleteDeliveryZoneAction;
use App\Actions\DeliveryZones\UpdateDeliveryZoneAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreDeliveryZoneRequest;
use App\Http\Requests\Backend\UpdateDeliveryZoneRequest;
use App\Models\DeliveryZone;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class DeliveryZoneController extends Controller
{
    /**
     * Display a listing of the delivery zones.
     */
    public function index(): Response
    {
        return Inertia::render('delivery-zones/Index', [
            'deliveryZones' => DeliveryZone::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new delivery zone.
     */
    public function create(): Response
    {
        return Inertia::render('delivery-zones/Create');
    }

    /**
     * Store a newly created delivery zone.
     */
    public function store(StoreDeliveryZoneRequest $request, CreateDeliveryZoneAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the delivery zone. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Delivery zone created.')]);

        return to_route('delivery-zones.index');
    }

    /**
     * Show the form for editing the specified delivery zone.
     */
    public function edit(DeliveryZone $deliveryZone): Response
    {
        return Inertia::render('delivery-zones/Edit', [
            'deliveryZone' => $deliveryZone,
        ]);
    }

    /**
     * Update the specified delivery zone.
     */
    public function update(UpdateDeliveryZoneRequest $request, DeliveryZone $deliveryZone, UpdateDeliveryZoneAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($deliveryZone, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the delivery zone. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Delivery zone updated.')]);

        return to_route('delivery-zones.index');
    }

    /**
     * Remove the specified delivery zone.
     */
    public function destroy(DeliveryZone $deliveryZone, DeleteDeliveryZoneAction $action): RedirectResponse
    {
        try {
            $action->handle($deliveryZone);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the delivery zone. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Delivery zone deleted.')]);

        return to_route('delivery-zones.index');
    }
}
