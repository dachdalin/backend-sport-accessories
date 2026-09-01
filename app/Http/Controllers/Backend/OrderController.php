<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Orders\CreateOrderAction;
use App\Actions\Orders\DeleteOrderAction;
use App\Actions\Orders\UpdateOrderAction;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\TaxType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreOrderRequest;
use App\Http\Requests\Backend\UpdateOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\BusinessSettingService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly BusinessSettingService $businessSettings,
    ) {}

    /**
     * Display a listing of the orders.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['order_status', 'payment_status', 'search']);

        return Inertia::render('orders/Index', [
            'orders' => $this->orderService->list($filters),
            'orderStatuses' => $this->orderStatusOptions(),
            'paymentStatuses' => $this->paymentStatusOptions(),
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(): Response
    {
        return Inertia::render('orders/Create', [
            'products' => $this->productOptions(),
            'orderStatuses' => $this->orderStatusOptions(),
            'paymentStatuses' => $this->paymentStatusOptions(),
            'discountTypes' => $this->discountTypeOptions(),
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(StoreOrderRequest $request, CreateOrderAction $action): RedirectResponse
    {
        $data = $request->safe()->except('items');
        $items = $request->validated('items');

        try {
            $action->handle($data, $items);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the order. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Order created.')]);

        return to_route('orders.index');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): Response
    {
        $settings = $this->businessSettings->all();

        return Inertia::render('orders/Show', [
            'order' => $order->load('items'),
            'business' => [
                'name' => $settings['site_name'] !== '' ? $settings['site_name'] : config('app.name'),
                'logoUrl' => $settings['logo'] !== 'def.png'
                    ? Storage::disk($settings['logo_storage_type'])->url($settings['logo'])
                    : null,
                'email' => $settings['contact_email'],
                'phone' => $settings['contact_phone'],
                'address' => $settings['address'],
                'currencySymbol' => $settings['currency_symbol'] !== '' ? $settings['currency_symbol'] : '$',
            ],
        ]);
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order): Response
    {
        return Inertia::render('orders/Edit', [
            'order' => $order->load('items'),
            'products' => $this->productOptions(),
            'orderStatuses' => $this->orderStatusOptions(),
            'paymentStatuses' => $this->paymentStatusOptions(),
            'discountTypes' => $this->discountTypeOptions(),
        ]);
    }

    /**
     * Update the specified order.
     */
    public function update(UpdateOrderRequest $request, Order $order, UpdateOrderAction $action): RedirectResponse
    {
        $data = $request->safe()->except('items');
        $items = $request->validated('items');

        try {
            $action->handle($order, $data, $items);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the order. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Order updated.')]);

        return to_route('orders.index');
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order, DeleteOrderAction $action): RedirectResponse
    {
        try {
            $action->handle($order);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the order. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Order deleted.')]);

        return to_route('orders.index');
    }

    /**
     * @return array<int, array{value: int, label: string, unit_price: string}>
     */
    private function productOptions(): array
    {
        return Product::query()
            ->orderBy('name')
            ->get(['id', 'name', 'unit_price'])
            ->map(fn (Product $product) => [
                'value' => $product->id,
                'label' => $product->name,
                'unit_price' => $product->unit_price,
            ])
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function orderStatusOptions(): array
    {
        return array_map(
            fn (OrderStatus $case) => ['value' => $case->value, 'label' => $case->label()],
            OrderStatus::cases(),
        );
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function paymentStatusOptions(): array
    {
        return array_map(
            fn (OrderPaymentStatus $case) => ['value' => $case->value, 'label' => $case->label()],
            OrderPaymentStatus::cases(),
        );
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function discountTypeOptions(): array
    {
        return array_map(
            fn (TaxType $case) => ['value' => $case->value, 'label' => $case->label()],
            TaxType::cases(),
        );
    }
}
