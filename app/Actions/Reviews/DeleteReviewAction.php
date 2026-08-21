<?php

namespace App\Actions\Reviews;

use App\Models\Review;
use Illuminate\Support\Facades\DB;

class DeleteReviewAction
{
    public function handle(Review $review): void
    {
        DB::transaction(function () use ($review) {
            $review->delete();
        });
    }
}
