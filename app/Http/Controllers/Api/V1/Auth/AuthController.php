<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Customers\FindOrCreateSocialCustomerAction;
use App\Actions\Customers\RegisterCustomerAction;
use App\Actions\Customers\ResetCustomerPasswordAction;
use App\Actions\Customers\SendPasswordResetCodeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Auth\GoogleLoginRequest;
use App\Http\Requests\Api\V1\Auth\LoginCustomerRequest;
use App\Http\Requests\Api\V1\Auth\RegisterCustomerRequest;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\TelegramLoginRequest;
use App\Http\Resources\Api\V1\CustomerResource;
use App\Models\Customer;
use App\Services\GoogleAuthService;
use App\Services\TelegramAuthService;
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

        $this->ensureActive($customer, 'email');

        return response()->json([
            'customer' => new CustomerResource($customer),
            'token' => $customer->createToken($request->validated('device_name'))->plainTextToken,
        ]);
    }

    /**
     * Sign in (or register) a customer using a verified Google Sign-In ID token, and issue an API token.
     */
    public function google(GoogleLoginRequest $request, GoogleAuthService $googleAuthService, FindOrCreateSocialCustomerAction $action): JsonResponse
    {
        $profile = $googleAuthService->verifyIdToken($request->validated('id_token'));

        $customer = $action->handle('google_id', $profile['id'], [
            'name' => $profile['name'],
            'email' => $profile['email'],
        ]);

        $this->ensureActive($customer, 'id_token');

        return response()->json([
            'customer' => new CustomerResource($customer),
            'token' => $customer->createToken($request->userAgent() ?? 'google')->plainTextToken,
        ]);
    }

    /**
     * Sign in (or register) a customer using a verified Telegram Login Widget payload, and issue an API token.
     */
    public function telegram(TelegramLoginRequest $request, TelegramAuthService $telegramAuthService, FindOrCreateSocialCustomerAction $action): JsonResponse
    {
        $profile = $telegramAuthService->verifyLoginPayload($request->validated());

        $name = trim(($profile['first_name'] ?? '').' '.($profile['last_name'] ?? ''));

        $customer = $action->handle('telegram_id', (string) $profile['id'], [
            'name' => $name !== '' ? $name : 'Telegram User',
            'email' => "telegram-{$profile['id']}@telegram.local",
        ]);

        $this->ensureActive($customer, 'hash');

        return response()->json([
            'customer' => new CustomerResource($customer),
            'token' => $customer->createToken($request->userAgent() ?? 'telegram')->plainTextToken,
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

    /**
     * Send a password reset code to the customer's email (via mail) or phone (via PlasGate SMS OTP).
     * Always responds the same way, whether or not the email/phone matches an account, to avoid
     * leaking which identifiers are registered.
     */
    public function forgotPassword(ForgotPasswordRequest $request, SendPasswordResetCodeAction $action): JsonResponse
    {
        $channel = $request->filled('email') ? 'email' : 'phone';

        $customer = Customer::query()->where($channel, $request->validated($channel))->first();

        if ($customer) {
            $action->handle($customer, $channel);
        }

        return response()->json([
            'message' => __('If an account matches, a reset code has been sent.'),
        ]);
    }

    /**
     * Reset the customer's password using the code sent by forgotPassword().
     */
    public function resetPassword(ResetPasswordRequest $request, ResetCustomerPasswordAction $action): JsonResponse
    {
        $channel = $request->filled('email') ? 'email' : 'phone';

        $customer = Customer::query()->where($channel, $request->validated($channel))->first();

        if (! $customer) {
            throw ValidationException::withMessages([
                'code' => [__('This reset code is invalid or has expired.')],
            ]);
        }

        $action->handle($customer, $request->validated('code'), $request->validated('password'));

        return response()->json([
            'message' => __('Your password has been reset.'),
        ]);
    }

    /**
     * Reject the sign-in attempt if the customer's account has been deactivated.
     */
    private function ensureActive(Customer $customer, string $field): void
    {
        if (! $customer->status) {
            throw ValidationException::withMessages([
                $field => [__('This account has been deactivated.')],
            ]);
        }
    }
}
