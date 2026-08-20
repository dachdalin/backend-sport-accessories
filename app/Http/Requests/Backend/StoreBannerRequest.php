<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['bail', 'required', 'string', 'max:100'],
            'image' => ['bail', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_alt_text' => ['bail', 'nullable', 'string', 'max:255'],
            'link_url' => ['bail', 'nullable', 'url', 'max:255'],
            'sort_order' => ['bail', 'sometimes', 'integer', 'min:0'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
