<?php

namespace App\Http\Controllers\Backend;

use App\Actions\RefundRequests\CreateRefundRequestAction;
use App\Actions\RefundRequests\DeleteRefundRequestAction;
use App\Actions\RefundRequests\UpdateRefundRequestAction;
use App\Enums\RefundStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreRefundRequestRequest;
use App\Http\Requests\Backend\UpdateRefundRequestRequest;
use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class RefundRequestController extends Controller
{
    /**
     * Display a listing of the refund requests.
     */
    public function index(): Response
    {
        return Inertia::render('refund-requests/Index', [
            'refundRequests' => RefundRequest::query()
                ->with(['order:id,order_number,customer_name', 'orderItem:id,product_name'])
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new refund request.
     */
    public function create(): Response
    {
        return Inertia::render('refund-requests/Create', [
            'orders' => $this->orderOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    /**
     * Store a newly created refund request.
     */
    public function store(StoreRefundRequestRequest $request, CreateRefundRequestAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the refund request. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Refund request created.')]);

        return to_route('refund-requests.index');
    }

    /**
     * Show the form for editing the specified refund request.
     */
    public function edit(RefundRequest $refundRequest): Response
    {
        return Inertia::render('refund-requests/Edit', [
            'refundRequest' => $refundRequest,
            'orders' => $this->orderOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    /**
     * Update the specified refund request.
     */
    public function update(UpdateRefundRequestRequest $request, RefundRequest $refundRequest, UpdateRefundRequestAction $action): RedirectResponse
    {
        try {
            $action->handle($refundRequest, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the refund request. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Refund request updated.')]);

        return to_route('refund-requests.index');
    }

    /**
     * Remove the specified refund request.
     */
    public function destroy(RefundRequest $refundRequest, DeleteRefundRequestAction $action): RedirectResponse
    {
        try {
            $action->handle($refundRequest);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the refund request. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Refund request deleted.')]);

        return to_route('refund-requests.index');
    }

    /**
     * @return array<int, array{value: int, label: string, items: array<int, array{value: int, label: string}>}>
     */
    private function orderOptions(): array
    {
        return Order::query()
            ->with('items:id,order_id,product_name')
            ->orderByDesc('id')
            ->get(['id', 'order_number', 'customer_name'])
            ->map(fn (Order $order) => [
                'value' => $order->id,
                'label' => "{$order->order_number} — {$order->customer_name}",
                'items' => $order->items
                    ->map(fn ($item) => ['value' => $item->id, 'label' => $item->product_name])
                    ->all(),
            ])
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function statusOptions(): array
    {
        return array_map(
            fn (RefundStatus $case) => ['value' => $case->value, 'label' => $case->label()],
            RefundStatus::cases(),
        );
    }
}
