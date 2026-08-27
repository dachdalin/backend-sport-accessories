<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use App\Notifications\CustomerPasswordResetCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SendPasswordResetCodeAction
{
    /**
     * @param  'email'|'phone'  $channel
     */
    public function handle(Customer $customer, string $channel): void
    {
        $code = (string) random_int(100000, 999999);

        DB::transaction(function () use ($customer, $channel, $code) {
            $customer->passwordResetCodes()->whereNull('used_at')->delete();

            $customer->passwordResetCodes()->create([
                'channel' => $channel,
                'code' => Hash::make($code),
                'expires_at' => now()->addMinutes(10),
            ]);
        });

        $customer->notify(new CustomerPasswordResetCode($code, $channel));
    }
}
