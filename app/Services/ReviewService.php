<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Database\Eloquent\Collection;

class ReviewService
{
    /**
     * List all reviews with their product, most recently submitted first.
     *
     * @return Collection<int, Review>
     */
    public function list(): Collection
    {
        return Review::query()
            ->with('product:id,name')
            ->latest()
            ->get();
    }
}
