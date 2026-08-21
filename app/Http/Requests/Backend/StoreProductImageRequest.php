<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductImageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => ['bail', 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['bail', 'nullable', 'integer', 'min:0'],
        ];
    }
}
