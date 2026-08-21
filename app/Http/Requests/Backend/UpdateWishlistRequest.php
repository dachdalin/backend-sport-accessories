<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWishlistRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['bail', 'required', 'integer', Rule::exists('products', 'id')],
            'customer_name' => ['bail', 'required', 'string', 'max:100'],
            'customer_email' => ['bail', 'nullable', 'email', 'max:191'],
        ];
    }
}
