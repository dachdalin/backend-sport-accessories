<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ListProductsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['bail', 'nullable', 'string', 'max:100'],
            'min_price' => ['bail', 'nullable', 'numeric', 'min:0'],
            'max_price' => ['bail', 'nullable', 'numeric', 'min:0', 'gte:min_price'],
            'rating' => ['bail', 'nullable', 'numeric', 'min:1', 'max:5'],
            'discounted' => ['bail', 'nullable', 'boolean'],
            'in_stock' => ['bail', 'nullable', 'boolean'],
        ];
    }
}
