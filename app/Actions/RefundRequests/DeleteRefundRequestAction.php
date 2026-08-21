<?php

namespace App\Actions\RefundRequests;

use App\Models\RefundRequest;
use Illuminate\Support\Facades\DB;

class DeleteRefundRequestAction
{
    public function handle(RefundRequest $refundRequest): void
    {
        DB::transaction(function () use ($refundRequest) {
            $refundRequest->delete();
        });
    }
}
