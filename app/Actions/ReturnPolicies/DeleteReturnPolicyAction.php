<?php

namespace App\Actions\ReturnPolicies;

use App\Models\ReturnPolicy;
use Illuminate\Support\Facades\DB;

class DeleteReturnPolicyAction
{
    public function handle(ReturnPolicy $returnPolicy): void
    {
        DB::transaction(function () use ($returnPolicy) {
            $returnPolicy->delete();
        });
    }
}
