<?php

namespace App\Http\Requests\Backend;

use App\Enums\TaxType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'variants' => ['bail', 'nullable', 'array'],
            'variants.*.color_id' => ['bail', 'nullable', 'integer', Rule::exists('colors', 'id')],
            'variants.*.size_id' => ['bail', 'nullable', 'integer', Rule::exists('sizes', 'id')],
            'variants.*.material_id' => ['bail', 'nullable', 'integer', Rule::exists('materials', 'id')],
            'variants.*.sku' => ['bail', 'nullable', 'string', 'max:100', 'distinct', Rule::unique('product_variants', 'sku')],
            'variants.*.extra_price' => ['bail', 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'variants.*.stock' => ['bail', 'required_with:variants', 'integer', 'min:0'],
            'attributes' => ['bail', 'nullable', 'array'],
            'attributes.*' => ['bail', 'integer', Rule::exists('attributes', 'id')],
        ];
    }

    /**
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $seen = [];

                foreach ($this->array('variants') as $index => $variant) {
                    if (! is_array($variant)) {
                        continue;
                    }

                    $key = implode('-', [
                        $variant['color_id'] ?? 'null',
                        $variant['size_id'] ?? 'null',
                        $variant['material_id'] ?? 'null',
                    ]);

                    if ($key === 'null-null-null') {
                        $validator->errors()->add("variants.{$index}", __('Each variant needs at least one of color, size, or material.'));

                        continue;
                    }

                    if (isset($seen[$key])) {
                        $validator->errors()->add("variants.{$index}", __('This color/size/material combination is already used by another variant.'));
                    }

                    $seen[$key] = true;
                }
            },
        ];
    }
}
