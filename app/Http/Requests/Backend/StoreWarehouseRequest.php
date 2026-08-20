<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreWarehouseRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => Str::upper($this->input('code'))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:100'],
            'code' => ['bail', 'required', 'string', 'max:20', 'regex:/^[A-Z0-9\-]+$/', 'unique:warehouses,code'],
            'address' => ['bail', 'nullable', 'string', 'max:255'],
            'city' => ['bail', 'nullable', 'string', 'max:100'],
            'country' => ['bail', 'nullable', 'string', 'max:100'],
            'phone' => ['bail', 'nullable', 'string', 'max:20'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
