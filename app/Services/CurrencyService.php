<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CurrencyService
{
    /**
     * List all currencies, most recently added first.
     *
     * @return Collection<int, Currency>
     */
    public function list(): Collection
    {
        return Currency::query()->latest()->get();
    }

    /**
     * Normalize currency input: codes are stored upper-case (ISO 4217 style).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function normalize(array $data): array
    {
        if (array_key_exists('code', $data)) {
            $data['code'] = Str::upper($data['code']);
        }

        return $data;
    }
}
