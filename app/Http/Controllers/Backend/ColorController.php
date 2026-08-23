<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Colors\CreateColorAction;
use App\Actions\Colors\DeleteColorAction;
use App\Actions\Colors\UpdateColorAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreColorRequest;
use App\Http\Requests\Backend\UpdateColorRequest;
use App\Models\Color;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ColorController extends Controller
{
    /**
     * Display a listing of the colors.
     */
    public function index(): Response
    {
        return Inertia::render('colors/Index', [
            'colors' => Color::query()->latest()->paginate(15)->withQueryString(),
        ]);
    }

    /**
     * Store a newly created color.
     */
    public function store(StoreColorRequest $request, CreateColorAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the color. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Color created.')]);

        return to_route('colors.index');
    }

    /**
     * Update the specified color.
     */
    public function update(UpdateColorRequest $request, Color $color, UpdateColorAction $action): RedirectResponse
    {
        try {
            $action->handle($color, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the color. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Color updated.')]);

        return to_route('colors.index');
    }

    /**
     * Remove the specified color.
     */
    public function destroy(Color $color, DeleteColorAction $action): RedirectResponse
    {
        try {
            $action->handle($color);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the color. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Color deleted.')]);

        return to_route('colors.index');
    }
}
