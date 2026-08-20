<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonialRequest extends FormRequest
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
            'customer_role' => ['bail', 'nullable', 'string', 'max:100'],
            'content' => ['bail', 'required', 'string', 'max:2000'],
            'rating' => ['bail', 'required', 'integer', 'between:1,5'],
            'avatar' => ['bail', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
