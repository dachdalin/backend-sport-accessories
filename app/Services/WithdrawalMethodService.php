<?php

namespace App\Services;

use App\Models\WithdrawalMethod;
use Illuminate\Database\Eloquent\Collection;

class WithdrawalMethodService
{
    /**
     * List all withdrawal methods, most recently added first.
     *
     * @return Collection<int, WithdrawalMethod>
     */
    public function list(): Collection
    {
        return WithdrawalMethod::query()->latest()->get();
    }
}
