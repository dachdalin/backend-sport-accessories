<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('coupon_code')) {
            $this->merge(['coupon_code' => Str::upper($this->input('coupon_code'))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shipping_address_id' => [
                'bail', 'required', 'integer',
                Rule::exists('shipping_addresses', 'id')->where('customer_id', $this->user()->id),
            ],
            'shipping_method_id' => ['bail', 'nullable', 'integer', Rule::exists('shipping_methods', 'id')],
            'payment_method' => ['bail', 'nullable', 'string', 'max:50'],
            'coupon_code' => ['bail', 'nullable', 'string', 'max:30'],
            'order_note' => ['bail', 'nullable', 'string', 'max:2000'],
        ];
    }
}
