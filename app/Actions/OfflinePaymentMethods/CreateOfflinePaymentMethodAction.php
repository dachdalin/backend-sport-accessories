<?php

namespace App\Actions\OfflinePaymentMethods;

use App\Models\OfflinePaymentMethod;

class CreateOfflinePaymentMethodAction
{
    /**
     * @param  array{method_name: string, method_fields: string, method_informations: string, status?: bool}  $data
     */
    public function handle(array $data): OfflinePaymentMethod
    {
        return OfflinePaymentMethod::create($data);
    }
}
