<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Materials\CreateMaterialAction;
use App\Actions\Materials\DeleteMaterialAction;
use App\Actions\Materials\UpdateMaterialAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreMaterialRequest;
use App\Http\Requests\Backend\UpdateMaterialRequest;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class MaterialController extends Controller
{
    /**
     * Display a listing of the materials.
     */
    public function index(): Response
    {
        return Inertia::render('materials/Index', [
            'materials' => Material::query()->latest()->get(),
        ]);
    }

    /**
     * Store a newly created material.
     */
    public function store(StoreMaterialRequest $request, CreateMaterialAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the material. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Material created.')]);

        return to_route('materials.index');
    }

    /**
     * Update the specified material.
     */
    public function update(UpdateMaterialRequest $request, Material $material, UpdateMaterialAction $action): RedirectResponse
    {
        try {
            $action->handle($material, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the material. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Material updated.')]);

        return to_route('materials.index');
    }

    /**
     * Remove the specified material.
     */
    public function destroy(Material $material, DeleteMaterialAction $action): RedirectResponse
    {
        try {
            $action->handle($material);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the material. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Material deleted.')]);

        return to_route('materials.index');
    }
}
