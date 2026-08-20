<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWithdrawalMethodRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'method_name' => ['bail', 'required', 'string', 'max:255', Rule::unique('withdrawal_methods', 'method_name')->ignore($this->route('withdrawal_method'))],
            'method_fields' => ['bail', 'required', 'string', 'max:5000'],
            'is_default' => ['bail', 'sometimes', 'boolean'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
