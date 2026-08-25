<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Customers\RegisterCustomerAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginCustomerRequest;
use App\Http\Requests\Api\V1\Auth\RegisterCustomerRequest;
use App\Http\Resources\Api\V1\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new customer and issue an API token.
     */
    public function register(RegisterCustomerRequest $request, RegisterCustomerAction $action): JsonResponse
    {
        $customer = $action->handle($request->validated());

        return response()->json([
            'customer' => new CustomerResource($customer),
            'token' => $customer->createToken($request->userAgent() ?? 'api')->plainTextToken,
        ], 201);
    }

    /**
     * Authenticate a customer and issue an API token.
     */
    public function login(LoginCustomerRequest $request): JsonResponse
    {
        $customer = Customer::query()->where('email', $request->validated('email'))->first();

        if (! $customer || ! $customer->password || ! Hash::check($request->validated('password'), $customer->password)) {
            throw ValidationException::withMessages([
                'email' => [__('The provided credentials are incorrect.')],
            ]);
        }

        if (! $customer->status) {
            throw ValidationException::withMessages([
                'email' => [__('This account has been deactivated.')],
            ]);
        }

        return response()->json([
            'customer' => new CustomerResource($customer),
            'token' => $customer->createToken($request->validated('device_name'))->plainTextToken,
        ]);
    }

    /**
     * Revoke the customer's current API token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(status: 204);
    }
}
