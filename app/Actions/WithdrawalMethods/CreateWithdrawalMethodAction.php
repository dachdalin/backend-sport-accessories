<?php

namespace App\Actions\WithdrawalMethods;

use App\Models\WithdrawalMethod;

class CreateWithdrawalMethodAction
{
    /**
     * @param  array{method_name: string, method_fields: string, is_default?: bool, status?: bool}  $data
     */
    public function handle(array $data): WithdrawalMethod
    {
        return WithdrawalMethod::create($data);
    }
}
