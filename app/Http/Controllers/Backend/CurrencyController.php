<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Currencies\CreateCurrencyAction;
use App\Actions\Currencies\DeleteCurrencyAction;
use App\Actions\Currencies\UpdateCurrencyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreCurrencyRequest;
use App\Http\Requests\Backend\UpdateCurrencyRequest;
use App\Models\Currency;
use App\Services\CurrencyService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CurrencyController extends Controller
{
    public function __construct(private CurrencyService $currencies) {}

    /**
     * Display a listing of the currencies.
     */
    public function index(): Response
    {
        return Inertia::render('currencies/Index', [
            'currencies' => $this->currencies->list(),
        ]);
    }

    /**
     * Show the form for creating a new currency.
     */
    public function create(): Response
    {
        return Inertia::render('currencies/Create');
    }

    /**
     * Store a newly created currency.
     */
    public function store(StoreCurrencyRequest $request, CreateCurrencyAction $action): RedirectResponse
    {
        $action->handle($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Currency created.')]);

        return to_route('currencies.index');
    }

    /**
     * Show the form for editing the specified currency.
     */
    public function edit(Currency $currency): Response
    {
        return Inertia::render('currencies/Edit', [
            'currency' => $currency,
        ]);
    }

    /**
     * Update the specified currency.
     */
    public function update(UpdateCurrencyRequest $request, Currency $currency, UpdateCurrencyAction $action): RedirectResponse
    {
        $action->handle($currency, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Currency updated.')]);

        return to_route('currencies.index');
    }

    /**
     * Remove the specified currency.
     */
    public function destroy(Currency $currency, DeleteCurrencyAction $action): RedirectResponse
    {
        $action->handle($currency);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Currency deleted.')]);

        return to_route('currencies.index');
    }
}
