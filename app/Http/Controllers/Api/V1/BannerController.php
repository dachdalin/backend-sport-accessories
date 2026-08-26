<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BannerResource;
use App\Models\Banner;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BannerController extends Controller
{
    /**
     * Display a paginated listing of the active banners, ordered for display.
     */
    public function index(): AnonymousResourceCollection
    {
        return BannerResource::collection(
            Banner::query()->where('status', true)->orderBy('sort_order')->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active banner.
     */
    public function show(Banner $banner): BannerResource
    {
        abort_unless($banner->status, 404);

        return new BannerResource($banner);
    }
}
