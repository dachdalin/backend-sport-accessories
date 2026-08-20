<?php

namespace App\Http\Controllers\Backend;

use App\Actions\WithdrawalMethods\CreateWithdrawalMethodAction;
use App\Actions\WithdrawalMethods\DeleteWithdrawalMethodAction;
use App\Actions\WithdrawalMethods\UpdateWithdrawalMethodAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreWithdrawalMethodRequest;
use App\Http\Requests\Backend\UpdateWithdrawalMethodRequest;
use App\Models\WithdrawalMethod;
use App\Services\WithdrawalMethodService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WithdrawalMethodController extends Controller
{
    public function __construct(private WithdrawalMethodService $withdrawalMethods) {}

    /**
     * Display a listing of the withdrawal methods.
     */
    public function index(): Response
    {
        return Inertia::render('withdrawal-methods/Index', [
            'withdrawalMethods' => $this->withdrawalMethods->list(),
        ]);
    }

    /**
     * Show the form for creating a new withdrawal method.
     */
    public function create(): Response
    {
        return Inertia::render('withdrawal-methods/Create');
    }

    /**
     * Store a newly created withdrawal method.
     */
    public function store(StoreWithdrawalMethodRequest $request, CreateWithdrawalMethodAction $action): RedirectResponse
    {
        $action->handle($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Withdrawal method created.')]);

        return to_route('withdrawal-methods.index');
    }

    /**
     * Show the form for editing the specified withdrawal method.
     */
    public function edit(WithdrawalMethod $withdrawalMethod): Response
    {
        return Inertia::render('withdrawal-methods/Edit', [
            'withdrawalMethod' => $withdrawalMethod,
        ]);
    }

    /**
     * Update the specified withdrawal method.
     */
    public function update(UpdateWithdrawalMethodRequest $request, WithdrawalMethod $withdrawalMethod, UpdateWithdrawalMethodAction $action): RedirectResponse
    {
        $action->handle($withdrawalMethod, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Withdrawal method updated.')]);

        return to_route('withdrawal-methods.index');
    }

    /**
     * Remove the specified withdrawal method.
     */
    public function destroy(WithdrawalMethod $withdrawalMethod, DeleteWithdrawalMethodAction $action): RedirectResponse
    {
        $action->handle($withdrawalMethod);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Withdrawal method deleted.')]);

        return to_route('withdrawal-methods.index');
    }
}
