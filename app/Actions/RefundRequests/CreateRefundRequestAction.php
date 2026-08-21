<?php

namespace App\Actions\RefundRequests;

use App\Models\RefundRequest;
use Illuminate\Support\Facades\DB;

class CreateRefundRequestAction
{
    /**
     * @param  array{order_id: int, order_item_id: ?int, amount: float, reason: string, status: string, admin_note: ?string}  $data
     */
    public function handle(array $data): RefundRequest
    {
        return DB::transaction(fn () => RefundRequest::create($data));
    }
}
