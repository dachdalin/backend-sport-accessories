<?php

namespace App\Services;

use App\Models\OfflinePaymentMethod;
use Illuminate\Database\Eloquent\Collection;

class OfflinePaymentMethodService
{
    /**
     * List all offline payment methods, most recently added first.
     *
     * @return Collection<int, OfflinePaymentMethod>
     */
    public function list(): Collection
    {
        return OfflinePaymentMethod::query()->latest()->get();
    }
}
