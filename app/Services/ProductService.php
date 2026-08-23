<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * List products with relationships, most recently added first.
     *
     * @return LengthAwarePaginator<int, Product>
     */
    public function list(): LengthAwarePaginator
    {
        return Product::query()
            ->with(['category:id,name', 'brand:id,name'])
            ->latest()
            ->paginate(15)
            ->withQueryString();
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
