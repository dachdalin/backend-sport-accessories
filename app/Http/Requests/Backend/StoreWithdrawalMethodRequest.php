<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreWithdrawalMethodRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'method_name' => ['bail', 'required', 'string', 'max:255', 'unique:withdrawal_methods,method_name'],
            'method_fields' => ['bail', 'required', 'string', 'max:5000'],
            'is_default' => ['bail', 'sometimes', 'boolean'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
