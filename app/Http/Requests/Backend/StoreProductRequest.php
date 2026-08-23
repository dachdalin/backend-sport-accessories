<?php

namespace App\Http\Requests\Backend;

use App\Enums\TaxType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:80', 'unique:products,name'],
            'code' => ['bail', 'nullable', 'string', 'max:50'],
            'description' => ['bail', 'nullable', 'string', 'max:10000'],
            'thumbnail' => ['bail', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'images' => ['bail', 'nullable', 'array', 'max:10'],
            'images.*' => ['bail', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'unit_price' => ['bail', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'purchase_price' => ['bail', 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'current_stock' => ['bail', 'required', 'integer', 'min:0'],
            'minimum_order_qty' => ['bail', 'required', 'integer', 'min:1'],
            'category_id' => ['bail', 'nullable', 'integer', Rule::exists('categories', 'id')],
            'brand_id' => ['bail', 'nullable', 'integer', Rule::exists('brands', 'id')],
            'tax' => ['bail', 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'tax_type' => ['bail', 'nullable', 'string', Rule::enum(TaxType::class)],
            'discount' => ['bail', 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'discount_type' => ['bail', 'nullable', 'string', Rule::enum(TaxType::class)],
            'free_shipping' => ['bail', 'sometimes', 'boolean'],
            'refundable' => ['bail', 'sometimes', 'boolean'],
            'featured' => ['bail', 'sometimes', 'boolean'],
            'meta_title' => ['bail', 'nullable', 'string', 'max:191'],
            'meta_description' => ['bail', 'nullable', 'string', 'max:191'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
