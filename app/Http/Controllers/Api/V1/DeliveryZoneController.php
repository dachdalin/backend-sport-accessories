<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DeliveryZoneResource;
use App\Models\DeliveryZone;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeliveryZoneController extends Controller
{
    /**
     * Display a paginated listing of the active delivery zones.
     */
    public function index(): AnonymousResourceCollection
    {
        return DeliveryZoneResource::collection(
            DeliveryZone::query()->where('status', true)->orderBy('zip_code')->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active delivery zone.
     */
    public function show(DeliveryZone $deliveryZone): DeliveryZoneResource
    {
        abort_unless($deliveryZone->status, 404);

        return new DeliveryZoneResource($deliveryZone);
    }
}
