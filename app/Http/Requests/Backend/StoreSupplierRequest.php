<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:150', 'unique:suppliers,name'],
            'contact_person' => ['bail', 'nullable', 'string', 'max:100'],
            'email' => ['bail', 'nullable', 'email', 'max:255', 'unique:suppliers,email'],
            'phone' => ['bail', 'nullable', 'string', 'max:20'],
            'address' => ['bail', 'nullable', 'string', 'max:255'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
