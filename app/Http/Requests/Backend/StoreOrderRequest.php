<?php

namespace App\Http\Requests\Backend;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\TaxType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['bail', 'required', 'string', 'max:100'],
            'customer_email' => ['bail', 'nullable', 'email', 'max:191'],
            'customer_phone' => ['bail', 'nullable', 'string', 'max:30'],
            'shipping_address' => ['bail', 'required', 'string', 'max:1000'],
            'order_status' => ['bail', 'required', 'string', Rule::enum(OrderStatus::class)],
            'payment_status' => ['bail', 'required', 'string', Rule::enum(OrderPaymentStatus::class)],
            'payment_method' => ['bail', 'nullable', 'string', 'max:50'],
            'discount_amount' => ['bail', 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'discount_type' => ['bail', 'nullable', 'string', Rule::enum(TaxType::class)],
            'shipping_cost' => ['bail', 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'order_note' => ['bail', 'nullable', 'string', 'max:2000'],
            'items' => ['bail', 'required', 'array', 'min:1'],
            'items.*.product_id' => ['bail', 'required', 'integer', Rule::exists('products', 'id')],
            'items.*.quantity' => ['bail', 'required', 'integer', 'min:1'],
            'items.*.unit_price' => ['bail', 'required', 'numeric', 'min:0', 'max:99999999.99'],
        ];
    }
}
