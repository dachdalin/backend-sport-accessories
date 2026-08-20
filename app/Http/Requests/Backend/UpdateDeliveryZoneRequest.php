<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeliveryZoneRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'zip_code' => ['bail', 'required', 'string', 'max:20', Rule::unique('delivery_zones', 'zip_code')->ignore($this->route('delivery_zone'))],
            'city' => ['bail', 'nullable', 'string', 'max:100'],
            'delivery_charge' => ['bail', 'required', 'numeric', 'min:0'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
