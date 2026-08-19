<?php

namespace App\Http\Controllers\Backend;

use App\Actions\ShippingMethods\CreateShippingMethodAction;
use App\Actions\ShippingMethods\DeleteShippingMethodAction;
use App\Actions\ShippingMethods\UpdateShippingMethodAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreShippingMethodRequest;
use App\Http\Requests\Backend\UpdateShippingMethodRequest;
use App\Models\ShippingMethod;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ShippingMethodController extends Controller
{
    /**
     * Display a listing of the shipping methods.
     */
    public function index(): Response
    {
        return Inertia::render('shipping-methods/Index', [
            'shippingMethods' => ShippingMethod::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new shipping method.
     */
    public function create(): Response
    {
        return Inertia::render('shipping-methods/Create');
    }

    /**
     * Store a newly created shipping method.
     */
    public function store(StoreShippingMethodRequest $request, CreateShippingMethodAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');
        $data['creator_id'] = $request->user()->id;
        $data['creator_type'] = 'admin';

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the shipping method. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Shipping method created.')]);

        return to_route('shipping-methods.index');
    }

    /**
     * Show the form for editing the specified shipping method.
     */
    public function edit(ShippingMethod $shippingMethod): Response
    {
        return Inertia::render('shipping-methods/Edit', [
            'shippingMethod' => $shippingMethod,
        ]);
    }

    /**
     * Update the specified shipping method.
     */
    public function update(UpdateShippingMethodRequest $request, ShippingMethod $shippingMethod, UpdateShippingMethodAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($shippingMethod, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the shipping method. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Shipping method updated.')]);

        return to_route('shipping-methods.index');
    }

    /**
     * Remove the specified shipping method.
     */
    public function destroy(ShippingMethod $shippingMethod, DeleteShippingMethodAction $action): RedirectResponse
    {
        try {
            $action->handle($shippingMethod);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the shipping method. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Shipping method deleted.')]);

        return to_route('shipping-methods.index');
    }
}
