<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * List all products with relationships, most recently added first.
     *
     * @return Collection<int, Product>
     */
    public function list(): Collection
    {
        return Product::query()
            ->with(['category:id,name', 'brand:id,name'])
            ->latest()
            ->get();
    }

    /**
     * Generate a unique slug from the product name.
     *
     * Appends a numeric suffix when a duplicate is found,
     * optionally ignoring a specific record (for updates).
     */
    public function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Product::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
