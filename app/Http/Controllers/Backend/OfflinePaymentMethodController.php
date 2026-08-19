<?php

namespace App\Http\Controllers\Backend;

use App\Actions\OfflinePaymentMethods\CreateOfflinePaymentMethodAction;
use App\Actions\OfflinePaymentMethods\DeleteOfflinePaymentMethodAction;
use App\Actions\OfflinePaymentMethods\UpdateOfflinePaymentMethodAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreOfflinePaymentMethodRequest;
use App\Http\Requests\Backend\UpdateOfflinePaymentMethodRequest;
use App\Models\OfflinePaymentMethod;
use App\Services\OfflinePaymentMethodService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OfflinePaymentMethodController extends Controller
{
    public function __construct(private OfflinePaymentMethodService $offlinePaymentMethods) {}

    /**
     * Display a listing of the offline payment methods.
     */
    public function index(): Response
    {
        return Inertia::render('offline-payment-methods/Index', [
            'offlinePaymentMethods' => $this->offlinePaymentMethods->list(),
        ]);
    }

    /**
     * Show the form for creating a new offline payment method.
     */
    public function create(): Response
    {
        return Inertia::render('offline-payment-methods/Create');
    }

    /**
     * Store a newly created offline payment method.
     */
    public function store(StoreOfflinePaymentMethodRequest $request, CreateOfflinePaymentMethodAction $action): RedirectResponse
    {
        $action->handle($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Payment method created.')]);

        return to_route('offline-payment-methods.index');
    }

    /**
     * Show the form for editing the specified offline payment method.
     */
    public function edit(OfflinePaymentMethod $offlinePaymentMethod): Response
    {
        return Inertia::render('offline-payment-methods/Edit', [
            'offlinePaymentMethod' => $offlinePaymentMethod,
        ]);
    }

    /**
     * Update the specified offline payment method.
     */
    public function update(UpdateOfflinePaymentMethodRequest $request, OfflinePaymentMethod $offlinePaymentMethod, UpdateOfflinePaymentMethodAction $action): RedirectResponse
    {
        $action->handle($offlinePaymentMethod, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Payment method updated.')]);

        return to_route('offline-payment-methods.index');
    }

    /**
     * Remove the specified offline payment method.
     */
    public function destroy(OfflinePaymentMethod $offlinePaymentMethod, DeleteOfflinePaymentMethodAction $action): RedirectResponse
    {
        $action->handle($offlinePaymentMethod);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Payment method deleted.')]);

        return to_route('offline-payment-methods.index');
    }
}
