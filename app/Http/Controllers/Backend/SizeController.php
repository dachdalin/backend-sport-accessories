<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Sizes\CreateSizeAction;
use App\Actions\Sizes\DeleteSizeAction;
use App\Actions\Sizes\UpdateSizeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreSizeRequest;
use App\Http\Requests\Backend\UpdateSizeRequest;
use App\Models\Size;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class SizeController extends Controller
{
    /**
     * Display a listing of the sizes.
     */
    public function index(): Response
    {
        return Inertia::render('sizes/Index', [
            'sizes' => Size::query()->latest()->paginate(15)->withQueryString(),
        ]);
    }

    /**
     * Store a newly created size.
     */
    public function store(StoreSizeRequest $request, CreateSizeAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the size. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Size created.')]);

        return to_route('sizes.index');
    }

    /**
     * Update the specified size.
     */
    public function update(UpdateSizeRequest $request, Size $size, UpdateSizeAction $action): RedirectResponse
    {
        try {
            $action->handle($size, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the size. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Size updated.')]);

        return to_route('sizes.index');
    }

    /**
     * Remove the specified size.
     */
    public function destroy(Size $size, DeleteSizeAction $action): RedirectResponse
    {
        try {
            $action->handle($size);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the size. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Size deleted.')]);

        return to_route('sizes.index');
    }
}
