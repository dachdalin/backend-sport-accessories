<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'unique:brands,name'],
            'image' => ['nullable', 'image', 'max:2048'],
            'image_alt_text' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
