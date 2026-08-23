<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Brands\CreateBrandAction;
use App\Actions\Brands\DeleteBrandAction;
use App\Actions\Brands\UpdateBrandAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreBrandRequest;
use App\Http\Requests\Backend\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class BrandController extends Controller
{
    /**
     * Display a listing of the brands.
     */
    public function index(): Response
    {
        return Inertia::render('brands/Index', [
            'brands' => Brand::query()->latest()->paginate(15)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create(): Response
    {
        return Inertia::render('brands/Create');
    }

    /**
     * Store a newly created brand.
     */
    public function store(StoreBrandRequest $request, CreateBrandAction $action): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data, $request->file('image'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the brand. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Brand created.')]);

        return to_route('brands.index');
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand): Response
    {
        return Inertia::render('brands/Edit', [
            'brand' => $brand,
        ]);
    }

    /**
     * Update the specified brand.
     */
    public function update(UpdateBrandRequest $request, Brand $brand, UpdateBrandAction $action): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($brand, $data, $request->file('image'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the brand. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Brand updated.')]);

        return to_route('brands.index');
    }

    /**
     * Remove the specified brand.
     */
    public function destroy(Brand $brand, DeleteBrandAction $action): RedirectResponse
    {
        try {
            $action->handle($brand);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the brand. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Brand deleted.')]);

        return to_route('brands.index');
    }
}
