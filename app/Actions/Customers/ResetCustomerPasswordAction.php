<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ResetCustomerPasswordAction
{
    public function handle(Customer $customer, string $code, string $password): void
    {
        DB::transaction(function () use ($customer, $code, $password) {
            $resetCode = $customer->passwordResetCodes()
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            if (! $resetCode || ! Hash::check($code, $resetCode->code)) {
                throw ValidationException::withMessages([
                    'code' => [__('This reset code is invalid or has expired.')],
                ]);
            }

            $resetCode->update(['used_at' => now()]);

            $customer->update(['password' => $password]);
            $customer->tokens()->delete();
        });
    }
}
