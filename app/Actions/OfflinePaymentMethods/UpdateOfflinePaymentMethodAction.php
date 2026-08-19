<?php

namespace App\Actions\OfflinePaymentMethods;

use App\Models\OfflinePaymentMethod;

class UpdateOfflinePaymentMethodAction
{
    /**
     * @param  array{method_name: string, method_fields: string, method_informations: string, status?: bool}  $data
     */
    public function handle(OfflinePaymentMethod $offlinePaymentMethod, array $data): OfflinePaymentMethod
    {
        $offlinePaymentMethod->update($data);

        return $offlinePaymentMethod;
    }
}
