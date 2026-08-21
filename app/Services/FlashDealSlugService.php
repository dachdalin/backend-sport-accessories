<?php

namespace App\Services;

use App\Models\FlashDeal;
use Illuminate\Support\Str;

class FlashDealSlugService
{
    /**
     * Generate a unique slug for a flash deal title.
     */
    public function generate(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    protected function slugExists(string $slug, ?int $ignoreId): bool
    {
        return FlashDeal::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }
}
