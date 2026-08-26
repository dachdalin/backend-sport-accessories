<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\ShippingAddresses\CreateShippingAddressAction;
use App\Actions\ShippingAddresses\DeleteShippingAddressAction;
use App\Actions\ShippingAddresses\UpdateShippingAddressAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreShippingAddressRequest;
use App\Http\Requests\Api\V1\UpdateShippingAddressRequest;
use App\Http\Resources\Api\V1\ShippingAddressResource;
use App\Models\ShippingAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShippingAddressController extends Controller
{
    /**
     * Display a paginated listing of the authenticated customer's shipping addresses.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return ShippingAddressResource::collection(
            ShippingAddress::query()
                ->where('customer_id', $request->user()->id)
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        );
    }

    /**
     * Store a newly created shipping address for the authenticated customer.
     */
    public function store(StoreShippingAddressRequest $request, CreateShippingAddressAction $action): JsonResponse
    {
        $data = $request->safe()->except('is_default');
        $data['customer_id'] = $request->user()->id;
        $data['is_default'] = $request->boolean('is_default');

        $shippingAddress = $action->handle($data);

        return (new ShippingAddressResource($shippingAddress))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    /**
     * Update the specified shipping address belonging to the authenticated customer.
     */
    public function update(UpdateShippingAddressRequest $request, ShippingAddress $shippingAddress, UpdateShippingAddressAction $action): ShippingAddressResource
    {
        abort_unless($shippingAddress->customer_id === $request->user()->id, 404);

        $data = $request->safe()->except('is_default');
        $data['customer_id'] = $shippingAddress->customer_id;
        $data['is_default'] = $request->boolean('is_default');

        return new ShippingAddressResource($action->handle($shippingAddress, $data));
    }

    /**
     * Remove the specified shipping address belonging to the authenticated customer.
     */
    public function destroy(Request $request, ShippingAddress $shippingAddress, DeleteShippingAddressAction $action): JsonResponse
    {
        abort_unless($shippingAddress->customer_id === $request->user()->id, 404);

        $action->handle($shippingAddress);

        return response()->json(status: JsonResponse::HTTP_NO_CONTENT);
    }
}
