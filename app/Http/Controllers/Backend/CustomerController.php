<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Customers\CreateCustomerAction;
use App\Actions\Customers\DeleteCustomerAction;
use App\Actions\Customers\UpdateCustomerAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreCustomerRequest;
use App\Http\Requests\Backend\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(): Response
    {
        return Inertia::render('customers/Index', [
            'customers' => Customer::query()->latest()->paginate(15)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create(): Response
    {
        return Inertia::render('customers/Create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(StoreCustomerRequest $request, CreateCustomerAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the customer. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Customer created.')]);

        return to_route('customers.index');
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer): Response
    {
        return Inertia::render('customers/Edit', [
            'customer' => $customer->append(['provider', 'provider_id']),
        ]);
    }

    /**
     * Update the specified customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer, UpdateCustomerAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($customer, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the customer. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Customer updated.')]);

        return to_route('customers.index');
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer, DeleteCustomerAction $action): RedirectResponse
    {
        try {
            $action->handle($customer);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the customer. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Customer deleted.')]);

        return to_route('customers.index');
    }
}
