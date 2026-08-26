<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\StoreLocationResource;
use App\Models\StoreLocation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StoreLocationController extends Controller
{
    /**
     * Display a paginated listing of the active store locations.
     */
    public function index(): AnonymousResourceCollection
    {
        return StoreLocationResource::collection(
            StoreLocation::query()->where('status', true)->latest()->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active store location.
     */
    public function show(StoreLocation $storeLocation): StoreLocationResource
    {
        abort_unless($storeLocation->status, 404);

        return new StoreLocationResource($storeLocation);
    }
}
