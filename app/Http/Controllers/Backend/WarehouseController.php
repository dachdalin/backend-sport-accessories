<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Warehouses\CreateWarehouseAction;
use App\Actions\Warehouses\DeleteWarehouseAction;
use App\Actions\Warehouses\UpdateWarehouseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreWarehouseRequest;
use App\Http\Requests\Backend\UpdateWarehouseRequest;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the warehouses.
     */
    public function index(): Response
    {
        return Inertia::render('warehouses/Index', [
            'warehouses' => Warehouse::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function create(): Response
    {
        return Inertia::render('warehouses/Create');
    }

    /**
     * Store a newly created warehouse.
     */
    public function store(StoreWarehouseRequest $request, CreateWarehouseAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the warehouse. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Warehouse created.')]);

        return to_route('warehouses.index');
    }

    /**
     * Show the form for editing the specified warehouse.
     */
    public function edit(Warehouse $warehouse): Response
    {
        return Inertia::render('warehouses/Edit', [
            'warehouse' => $warehouse,
        ]);
    }

    /**
     * Update the specified warehouse.
     */
    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse, UpdateWarehouseAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($warehouse, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the warehouse. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Warehouse updated.')]);

        return to_route('warehouses.index');
    }

    /**
     * Remove the specified warehouse.
     */
    public function destroy(Warehouse $warehouse, DeleteWarehouseAction $action): RedirectResponse
    {
        try {
            $action->handle($warehouse);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the warehouse. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Warehouse deleted.')]);

        return to_route('warehouses.index');
    }
}
