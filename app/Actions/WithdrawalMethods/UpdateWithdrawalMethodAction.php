<?php

namespace App\Actions\WithdrawalMethods;

use App\Models\WithdrawalMethod;

class UpdateWithdrawalMethodAction
{
    /**
     * @param  array{method_name: string, method_fields: string, is_default?: bool, status?: bool}  $data
     */
    public function handle(WithdrawalMethod $withdrawalMethod, array $data): WithdrawalMethod
    {
        $withdrawalMethod->update($data);

        return $withdrawalMethod;
    }
}
