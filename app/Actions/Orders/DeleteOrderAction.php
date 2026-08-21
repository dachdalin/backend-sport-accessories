<?php

namespace App\Actions\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DeleteOrderAction
{
    public function handle(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->delete();
        });
    }
}
