<?php

namespace App\Http\Requests\Backend;

use App\Enums\TaxType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFlashDealRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string|Closure>
     */
    public function rules(): array
    {
        return [
            'title' => ['bail', 'required', 'string', 'max:150'],
            'start_date' => ['bail', 'required', 'date'],
            'end_date' => ['bail', 'required', 'date', 'after_or_equal:start_date'],
            'status' => ['bail', 'sometimes', 'boolean'],
            'featured' => ['bail', 'sometimes', 'boolean'],
            'background_color' => ['bail', 'nullable', 'string', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'text_color' => ['bail', 'nullable', 'string', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'banner' => ['bail', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'items' => ['bail', 'required', 'array', 'min:1'],
            'items.*.product_id' => ['bail', 'required', 'integer', Rule::exists('products', 'id')],
            'items.*.discount' => ['bail', 'required', 'numeric', 'min:0.01', 'max:99999999.99', $this->percentDiscountNotOver100()],
            'items.*.discount_type' => ['bail', 'required', 'string', Rule::enum(TaxType::class)],
        ];
    }

    /**
     * Reject a percentage discount above 100% for the item it belongs to.
     */
    private function percentDiscountNotOver100(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $index = explode('.', $attribute)[1];

            if ($this->input("items.$index.discount_type") === TaxType::Percent->value && $value > 100) {
                $fail(__('The :attribute may not be greater than 100 when the discount type is percentage.'));
            }
        };
    }
}
