<?php

namespace App\Http\Controllers\Backend;

use App\Actions\StoreLocations\CreateStoreLocationAction;
use App\Actions\StoreLocations\DeleteStoreLocationAction;
use App\Actions\StoreLocations\UpdateStoreLocationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreStoreLocationRequest;
use App\Http\Requests\Backend\UpdateStoreLocationRequest;
use App\Models\StoreLocation;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class StoreLocationController extends Controller
{
    /**
     * Display a listing of the store locations.
     */
    public function index(): Response
    {
        return Inertia::render('store-locations/Index', [
            'storeLocations' => StoreLocation::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new store location.
     */
    public function create(): Response
    {
        return Inertia::render('store-locations/Create');
    }

    /**
     * Store a newly created store location.
     */
    public function store(StoreStoreLocationRequest $request, CreateStoreLocationAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the store location. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Store location created.')]);

        return to_route('store-locations.index');
    }

    /**
     * Show the form for editing the specified store location.
     */
    public function edit(StoreLocation $storeLocation): Response
    {
        return Inertia::render('store-locations/Edit', [
            'storeLocation' => $storeLocation,
        ]);
    }

    /**
     * Update the specified store location.
     */
    public function update(UpdateStoreLocationRequest $request, StoreLocation $storeLocation, UpdateStoreLocationAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($storeLocation, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the store location. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Store location updated.')]);

        return to_route('store-locations.index');
    }

    /**
     * Remove the specified store location.
     */
    public function destroy(StoreLocation $storeLocation, DeleteStoreLocationAction $action): RedirectResponse
    {
        try {
            $action->handle($storeLocation);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the store location. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Store location deleted.')]);

        return to_route('store-locations.index');
    }
}
