<?php

namespace App\Actions\Reviews;

use App\Models\Review;
use Illuminate\Support\Facades\DB;

class CreateReviewAction
{
    /**
     * @param  array{product_id: int, customer_name: string, customer_email: ?string, rating: int, comment: string, admin_reply: ?string, status: string}  $data
     */
    public function handle(array $data): Review
    {
        return DB::transaction(function () use ($data) {
            return Review::create($data);
        });
    }
}
