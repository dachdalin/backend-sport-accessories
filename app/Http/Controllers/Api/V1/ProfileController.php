<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Customers\DeleteCustomerAction;
use App\Actions\Customers\UpdateCustomerAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\DeleteAccountRequest;
use App\Http\Requests\Api\V1\UpdateProfileRequest;
use App\Http\Resources\Api\V1\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the authenticated customer's profile.
     */
    public function show(Request $request): CustomerResource
    {
        return new CustomerResource($request->user());
    }

    /**
     * Update the authenticated customer's profile.
     */
    public function update(UpdateProfileRequest $request, UpdateCustomerAction $action): CustomerResource
    {
        $customer = $request->user();

        $data = $request->validated();
        $data['status'] = $customer->status;

        return new CustomerResource($action->handle($customer, $data));
    }

    /**
     * Delete the authenticated customer's account after confirming their password.
     */
    public function destroy(DeleteAccountRequest $request, DeleteCustomerAction $action): JsonResponse
    {
        $action->handle($request->user());

        return response()->json(status: JsonResponse::HTTP_NO_CONTENT);
    }
}
