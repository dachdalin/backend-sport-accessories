<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Suppliers\CreateSupplierAction;
use App\Actions\Suppliers\DeleteSupplierAction;
use App\Actions\Suppliers\UpdateSupplierAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreSupplierRequest;
use App\Http\Requests\Backend\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class SupplierController extends Controller
{
    /**
     * Display a listing of the suppliers.
     */
    public function index(): Response
    {
        return Inertia::render('suppliers/Index', [
            'suppliers' => Supplier::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create(): Response
    {
        return Inertia::render('suppliers/Create');
    }

    /**
     * Store a newly created supplier.
     */
    public function store(StoreSupplierRequest $request, CreateSupplierAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the supplier. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Supplier created.')]);

        return to_route('suppliers.index');
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier): Response
    {
        return Inertia::render('suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Update the specified supplier.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier, UpdateSupplierAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($supplier, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the supplier. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Supplier updated.')]);

        return to_route('suppliers.index');
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy(Supplier $supplier, DeleteSupplierAction $action): RedirectResponse
    {
        try {
            $action->handle($supplier);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the supplier. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Supplier deleted.')]);

        return to_route('suppliers.index');
    }
}
