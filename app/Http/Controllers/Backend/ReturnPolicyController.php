<?php

namespace App\Http\Controllers\Backend;

use App\Actions\ReturnPolicies\CreateReturnPolicyAction;
use App\Actions\ReturnPolicies\DeleteReturnPolicyAction;
use App\Actions\ReturnPolicies\UpdateReturnPolicyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreReturnPolicyRequest;
use App\Http\Requests\Backend\UpdateReturnPolicyRequest;
use App\Models\ReturnPolicy;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ReturnPolicyController extends Controller
{
    /**
     * Display a listing of the return policies.
     */
    public function index(): Response
    {
        return Inertia::render('return-policies/Index', [
            'returnPolicies' => ReturnPolicy::query()->latest()->get(),
        ]);
    }

    /**
     * Store a newly created return policy.
     */
    public function store(StoreReturnPolicyRequest $request, CreateReturnPolicyAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the return policy. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Return policy created.')]);

        return to_route('return-policies.index');
    }

    /**
     * Update the specified return policy.
     */
    public function update(UpdateReturnPolicyRequest $request, ReturnPolicy $returnPolicy, UpdateReturnPolicyAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($returnPolicy, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the return policy. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Return policy updated.')]);

        return to_route('return-policies.index');
    }

    /**
     * Remove the specified return policy.
     */
    public function destroy(ReturnPolicy $returnPolicy, DeleteReturnPolicyAction $action): RedirectResponse
    {
        try {
            $action->handle($returnPolicy);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the return policy. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Return policy deleted.')]);

        return to_route('return-policies.index');
    }
}
