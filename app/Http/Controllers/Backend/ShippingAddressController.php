<?php

namespace App\Http\Controllers\Backend;

use App\Actions\ShippingAddresses\CreateShippingAddressAction;
use App\Actions\ShippingAddresses\DeleteShippingAddressAction;
use App\Actions\ShippingAddresses\UpdateShippingAddressAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreShippingAddressRequest;
use App\Http\Requests\Backend\UpdateShippingAddressRequest;
use App\Models\Customer;
use App\Models\ShippingAddress;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ShippingAddressController extends Controller
{
    /**
     * Display a listing of the shipping addresses.
     */
    public function index(): Response
    {
        return Inertia::render('shipping-addresses/Index', [
            'shippingAddresses' => ShippingAddress::query()->with('customer:id,name')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new shipping address.
     */
    public function create(): Response
    {
        return Inertia::render('shipping-addresses/Create', [
            'customers' => $this->customerOptions(),
        ]);
    }

    /**
     * Store a newly created shipping address.
     */
    public function store(StoreShippingAddressRequest $request, CreateShippingAddressAction $action): RedirectResponse
    {
        $data = $request->safe()->except('is_default');
        $data['is_default'] = $request->boolean('is_default');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the shipping address. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Shipping address created.')]);

        return to_route('shipping-addresses.index');
    }

    /**
     * Show the form for editing the specified shipping address.
     */
    public function edit(ShippingAddress $shippingAddress): Response
    {
        return Inertia::render('shipping-addresses/Edit', [
            'shippingAddress' => $shippingAddress,
            'customers' => $this->customerOptions(),
        ]);
    }

    /**
     * Update the specified shipping address.
     */
    public function update(UpdateShippingAddressRequest $request, ShippingAddress $shippingAddress, UpdateShippingAddressAction $action): RedirectResponse
    {
        $data = $request->safe()->except('is_default');
        $data['is_default'] = $request->boolean('is_default');

        try {
            $action->handle($shippingAddress, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the shipping address. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Shipping address updated.')]);

        return to_route('shipping-addresses.index');
    }

    /**
     * Remove the specified shipping address.
     */
    public function destroy(ShippingAddress $shippingAddress, DeleteShippingAddressAction $action): RedirectResponse
    {
        try {
            $action->handle($shippingAddress);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the shipping address. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Shipping address deleted.')]);

        return to_route('shipping-addresses.index');
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function customerOptions(): array
    {
        return Customer::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Customer $customer) => ['value' => $customer->id, 'label' => $customer->name])
            ->all();
    }
}
