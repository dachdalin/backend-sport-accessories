<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Banners\CreateBannerAction;
use App\Actions\Banners\DeleteBannerAction;
use App\Actions\Banners\UpdateBannerAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreBannerRequest;
use App\Http\Requests\Backend\UpdateBannerRequest;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class BannerController extends Controller
{
    /**
     * Display a listing of the banners.
     */
    public function index(): Response
    {
        return Inertia::render('banners/Index', [
            'banners' => Banner::query()->orderBy('sort_order')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create(): Response
    {
        return Inertia::render('banners/Create');
    }

    /**
     * Store a newly created banner.
     */
    public function store(StoreBannerRequest $request, CreateBannerAction $action): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data, $request->file('image'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the banner. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Banner created.')]);

        return to_route('banners.index');
    }

    /**
     * Show the form for editing the specified banner.
     */
    public function edit(Banner $banner): Response
    {
        return Inertia::render('banners/Edit', [
            'banner' => $banner,
        ]);
    }

    /**
     * Update the specified banner.
     */
    public function update(UpdateBannerRequest $request, Banner $banner, UpdateBannerAction $action): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($banner, $data, $request->file('image'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the banner. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Banner updated.')]);

        return to_route('banners.index');
    }

    /**
     * Remove the specified banner.
     */
    public function destroy(Banner $banner, DeleteBannerAction $action): RedirectResponse
    {
        try {
            $action->handle($banner);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the banner. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Banner deleted.')]);

        return to_route('banners.index');
    }
}
