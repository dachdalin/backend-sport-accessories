<?php

namespace App\Services\Tags;

use App\Models\Tag;
use Illuminate\Support\Str;

class TagService
{
    /**
     * Trim and collapse internal whitespace so "  Running   Shoes " and
     * "Running Shoes" are treated as the same tag.
     */
    public function normalize(string $tag): string
    {
        return Str::of($tag)->trim()->squish()->value();
    }

    /**
     * Find an existing tag that matches the given (already normalized) tag
     * case-insensitively, optionally ignoring one record (for updates).
     */
    public function findDuplicate(string $normalizedTag, ?int $ignoreId = null): ?Tag
    {
        return Tag::query()
            ->whereRaw('LOWER(tag) = ?', [Str::lower($normalizedTag)])
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->first();
    }
}
