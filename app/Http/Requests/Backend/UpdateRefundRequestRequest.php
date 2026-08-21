<?php

namespace App\Http\Requests\Backend;

use App\Enums\RefundStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRefundRequestRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => ['bail', 'required', 'integer', Rule::exists('orders', 'id')],
            'order_item_id' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists('order_items', 'id')->where('order_id', $this->input('order_id')),
            ],
            'amount' => ['bail', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'reason' => ['bail', 'required', 'string', 'max:2000'],
            'status' => ['bail', 'required', 'string', Rule::enum(RefundStatus::class)],
            'admin_note' => ['bail', 'nullable', 'string', 'max:2000'],
        ];
    }
}
