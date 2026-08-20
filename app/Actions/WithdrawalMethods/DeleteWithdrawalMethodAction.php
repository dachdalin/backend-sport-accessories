<?php

namespace App\Actions\WithdrawalMethods;

use App\Models\WithdrawalMethod;

class DeleteWithdrawalMethodAction
{
    public function handle(WithdrawalMethod $withdrawalMethod): void
    {
        $withdrawalMethod->delete();
    }
}
