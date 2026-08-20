<?php

namespace App\Actions\ReturnPolicies;

use App\Models\ReturnPolicy;
use Illuminate\Support\Facades\DB;

class UpdateReturnPolicyAction
{
    /**
     * @param  array{title: string, description: string, days_allowed: int, status: bool}  $data
     */
    public function handle(ReturnPolicy $returnPolicy, array $data): ReturnPolicy
    {
        DB::transaction(function () use ($returnPolicy, $data) {
            $returnPolicy->update($data);
        });

        return $returnPolicy;
    }
}
