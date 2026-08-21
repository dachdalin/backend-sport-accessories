<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Attributes\CreateAttributeAction;
use App\Actions\Attributes\DeleteAttributeAction;
use App\Actions\Attributes\UpdateAttributeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreAttributeRequest;
use App\Http\Requests\Backend\UpdateAttributeRequest;
use App\Models\Attribute;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class AttributeController extends Controller
{
    /**
     * Display a listing of the attributes.
     */
    public function index(): Response
    {
        return Inertia::render('attributes/Index', [
            'attributes' => Attribute::query()->latest()->get(),
        ]);
    }

    /**
     * Store a newly created attribute.
     */
    public function store(StoreAttributeRequest $request, CreateAttributeAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the attribute. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Attribute created.')]);

        return to_route('attributes.index');
    }

    /**
     * Update the specified attribute.
     */
    public function update(UpdateAttributeRequest $request, Attribute $attribute, UpdateAttributeAction $action): RedirectResponse
    {
        try {
            $action->handle($attribute, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the attribute. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Attribute updated.')]);

        return to_route('attributes.index');
    }

    /**
     * Remove the specified attribute.
     */
    public function destroy(Attribute $attribute, DeleteAttributeAction $action): RedirectResponse
    {
        try {
            $action->handle($attribute);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the attribute. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Attribute deleted.')]);

        return to_route('attributes.index');
    }
}
