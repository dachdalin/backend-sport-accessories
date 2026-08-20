<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMaterialRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:30', 'regex:/^[\pL\s\-]+$/u', Rule::unique('materials', 'name')->ignore($this->route('material'))],
            'code' => ['bail', 'required', 'string', 'max:10', 'regex:/^[A-Z0-9]+$/', Rule::unique('materials', 'code')->ignore($this->route('material'))],
        ];
    }
}
