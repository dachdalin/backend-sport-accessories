<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReturnPolicyRequest extends FormRequest
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
            'description' => ['bail', 'required', 'string', 'max:5000'],
            'days_allowed' => ['bail', 'required', 'integer', 'min:0', 'max:365'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
