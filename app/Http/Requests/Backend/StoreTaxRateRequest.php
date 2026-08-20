<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaxRateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:100', 'unique:tax_rates,name'],
            'region' => ['bail', 'nullable', 'string', 'max:100'],
            'rate' => ['bail', 'required', 'numeric', 'min:0', 'max:100'],
            'is_default' => ['bail', 'sometimes', 'boolean'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
