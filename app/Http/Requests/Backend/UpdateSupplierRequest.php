<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:150', Rule::unique('suppliers', 'name')->ignore($this->route('supplier'))],
            'contact_person' => ['bail', 'nullable', 'string', 'max:100'],
            'email' => ['bail', 'nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')->ignore($this->route('supplier'))],
            'phone' => ['bail', 'nullable', 'string', 'max:20'],
            'address' => ['bail', 'nullable', 'string', 'max:255'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
