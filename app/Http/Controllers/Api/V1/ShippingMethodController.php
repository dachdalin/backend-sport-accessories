<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ShippingMethodResource;
use App\Models\ShippingMethod;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShippingMethodController extends Controller
{
    /**
     * Display a paginated listing of the active shipping methods.
     */
    public function index(): AnonymousResourceCollection
    {
        return ShippingMethodResource::collection(
            ShippingMethod::query()->where('status', true)->latest()->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active shipping method.
     */
    public function show(ShippingMethod $shippingMethod): ShippingMethodResource
    {
        abort_unless($shippingMethod->status, 404);

        return new ShippingMethodResource($shippingMethod);
    }
}
