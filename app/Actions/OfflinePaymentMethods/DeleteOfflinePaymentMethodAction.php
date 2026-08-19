<?php

namespace App\Actions\OfflinePaymentMethods;

use App\Models\OfflinePaymentMethod;

class DeleteOfflinePaymentMethodAction
{
    public function handle(OfflinePaymentMethod $offlinePaymentMethod): void
    {
        $offlinePaymentMethod->delete();
    }
}
