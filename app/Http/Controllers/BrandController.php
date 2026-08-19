<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    /**
     * Display a listing of the brands.
     */
    public function index(): Response
    {
        return Inertia::render('brands/Index', [
            'brands' => Brand::query()->latest()->get(),
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
    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['status'] = $request->boolean('status');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('brands', 'public');
        }

        Brand::create($data);

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
    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['status'] = $request->boolean('status');

        if ($request->hasFile('image')) {
            if ($brand->image !== 'def.png') {
                Storage::disk($brand->image_storage_type)->delete($brand->image);
            }

            $data['image'] = $request->file('image')->store('brands', 'public');
        }

        $brand->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Brand updated.')]);

        return to_route('brands.index');
    }

    /**
     * Remove the specified brand.
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->image !== 'def.png') {
            Storage::disk($brand->image_storage_type)->delete($brand->image);
        }

        $brand->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Brand deleted.')]);

        return to_route('brands.index');
    }
}
