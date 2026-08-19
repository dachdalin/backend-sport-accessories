<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;

class CategorySlugService
{
    /**
     * Generate a unique slug for a category name.
     */
    public function generate(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
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
        return Category::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }
}
