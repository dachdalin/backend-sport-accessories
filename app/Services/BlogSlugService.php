<?php

namespace App\Services;

use App\Models\Blog;
use Illuminate\Support\Str;

class BlogSlugService
{
    /**
     * Generate a unique slug for a blog title.
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
        return Blog::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }
}
