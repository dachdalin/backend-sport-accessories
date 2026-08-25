<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BrandController extends Controller
{
    /**
     * Display a listing of the active brands.
     */
    public function index(): AnonymousResourceCollection
    {
        return BrandResource::collection(
            Brand::query()->where('status', true)->latest()->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active brand.
     */
    public function show(Brand $brand): BrandResource
    {
        abort_unless($brand->status, 404);

        return new BrandResource($brand);
    }
}
