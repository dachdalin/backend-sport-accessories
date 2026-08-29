<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'email' => ['bail', 'required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['bail', 'nullable', 'string', 'max:25'],
            'address' => ['bail', 'nullable', 'string', 'max:255'],
            'balance' => ['bail', 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
