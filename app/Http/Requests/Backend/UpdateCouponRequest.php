<?php

namespace App\Http\Requests\Backend;

use App\Enums\CouponType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateCouponRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => Str::upper($this->input('code'))]);
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
            'code' => ['bail', 'required', 'string', 'max:30', 'regex:/^[A-Z0-9\-]+$/', Rule::unique('coupons', 'code')->ignore($this->route('coupon'))],
            'type' => ['bail', 'required', new Enum(CouponType::class)],
            'value' => [
                'bail', 'required', 'numeric', 'min:0.01',
                $this->input('type') === CouponType::Percentage->value ? 'max:100' : 'max:999999.99',
            ],
            'min_order_amount' => ['bail', 'nullable', 'numeric', 'min:0', 'max:999999.99'],
            'usage_limit' => ['bail', 'nullable', 'integer', 'min:1'],
            'expires_at' => ['bail', 'nullable', 'date'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
