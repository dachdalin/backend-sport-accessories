<?php

namespace App\Http\Requests\Backend;

use App\Enums\TaxType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDealOfTheDayRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['bail', 'required', 'string', 'max:150'],
            'product_id' => ['bail', 'required', 'integer', Rule::exists('products', 'id')],
            'discount' => ['bail', 'nullable', 'numeric', 'min:0', 'max:999999.99'],
            'discount_type' => ['bail', 'nullable', 'string', Rule::enum(TaxType::class)],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
