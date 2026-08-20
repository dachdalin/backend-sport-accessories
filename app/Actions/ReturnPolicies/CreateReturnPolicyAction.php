<?php

namespace App\Actions\ReturnPolicies;

use App\Models\ReturnPolicy;
use Illuminate\Support\Facades\DB;

class CreateReturnPolicyAction
{
    /**
     * @param  array{title: string, description: string, days_allowed: int, status: bool}  $data
     */
    public function handle(array $data): ReturnPolicy
    {
        return DB::transaction(fn () => ReturnPolicy::create($data));
    }
}
