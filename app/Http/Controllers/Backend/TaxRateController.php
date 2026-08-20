<?php

namespace App\Http\Controllers\Backend;

use App\Actions\TaxRates\CreateTaxRateAction;
use App\Actions\TaxRates\DeleteTaxRateAction;
use App\Actions\TaxRates\UpdateTaxRateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreTaxRateRequest;
use App\Http\Requests\Backend\UpdateTaxRateRequest;
use App\Models\TaxRate;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class TaxRateController extends Controller
{
    /**
     * Display a listing of the tax rates.
     */
    public function index(): Response
    {
        return Inertia::render('tax-rates/Index', [
            'taxRates' => TaxRate::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new tax rate.
     */
    public function create(): Response
    {
        return Inertia::render('tax-rates/Create');
    }

    /**
     * Store a newly created tax rate.
     */
    public function store(StoreTaxRateRequest $request, CreateTaxRateAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['is_default'] = $request->boolean('is_default');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the tax rate. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tax rate created.')]);

        return to_route('tax-rates.index');
    }

    /**
     * Show the form for editing the specified tax rate.
     */
    public function edit(TaxRate $taxRate): Response
    {
        return Inertia::render('tax-rates/Edit', [
            'taxRate' => $taxRate,
        ]);
    }

    /**
     * Update the specified tax rate.
     */
    public function update(UpdateTaxRateRequest $request, TaxRate $taxRate, UpdateTaxRateAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['is_default'] = $request->boolean('is_default');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($taxRate, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the tax rate. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tax rate updated.')]);

        return to_route('tax-rates.index');
    }

    /**
     * Remove the specified tax rate.
     */
    public function destroy(TaxRate $taxRate, DeleteTaxRateAction $action): RedirectResponse
    {
        try {
            $action->handle($taxRate);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the tax rate. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tax rate deleted.')]);

        return to_route('tax-rates.index');
    }
}
