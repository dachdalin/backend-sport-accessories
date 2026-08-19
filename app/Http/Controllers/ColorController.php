<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use App\Models\Color;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ColorController extends Controller
{
    /**
     * Display a listing of the colors.
     */
    public function index(): Response
    {
        return Inertia::render('colors/Index', [
            'colors' => Color::query()->latest()->get(),
        ]);
    }

    /**
     * Store a newly created color.
     */
    public function store(StoreColorRequest $request): RedirectResponse
    {
        Color::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Color created.')]);

        return to_route('colors.index');
    }

    /**
     * Update the specified color.
     */
    public function update(UpdateColorRequest $request, Color $color): RedirectResponse
    {
        $color->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Color updated.')]);

        return to_route('colors.index');
    }

    /**
     * Remove the specified color.
     */
    public function destroy(Color $color): RedirectResponse
    {
        $color->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Color deleted.')]);

        return to_route('colors.index');
    }
}
