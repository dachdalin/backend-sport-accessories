<?php

namespace App\Actions\RefundRequests;

use App\Models\RefundRequest;
use Illuminate\Support\Facades\DB;

class UpdateRefundRequestAction
{
    /**
     * @param  array{order_id: int, order_item_id: ?int, amount: float, reason: string, status: string, admin_note: ?string}  $data
     */
    public function handle(RefundRequest $refundRequest, array $data): RefundRequest
    {
        DB::transaction(function () use ($refundRequest, $data) {
            $refundRequest->update($data);
        });

        return $refundRequest;
    }
}
