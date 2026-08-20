<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStoreLocationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:100'],
            'address' => ['bail', 'required', 'string', 'max:255'],
            'city' => ['bail', 'required', 'string', 'max:100'],
            'phone' => ['bail', 'nullable', 'string', 'max:30'],
            'opening_hours' => ['bail', 'nullable', 'string', 'max:100'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
