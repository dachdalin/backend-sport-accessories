<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OfflinePaymentMethodResource;
use App\Models\OfflinePaymentMethod;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OfflinePaymentMethodController extends Controller
{
    /**
     * Display a paginated listing of the active offline payment methods.
     */
    public function index(): AnonymousResourceCollection
    {
        return OfflinePaymentMethodResource::collection(
            OfflinePaymentMethod::query()->where('status', true)->latest()->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active offline payment method.
     */
    public function show(OfflinePaymentMethod $offlinePaymentMethod): OfflinePaymentMethodResource
    {
        abort_unless($offlinePaymentMethod->status, 404);

        return new OfflinePaymentMethodResource($offlinePaymentMethod);
    }
}
