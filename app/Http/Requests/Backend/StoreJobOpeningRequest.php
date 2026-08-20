<?php

namespace App\Http\Requests\Backend;

use App\Enums\EmploymentType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreJobOpeningRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['bail', 'required', 'string', 'max:150'],
            'department' => ['bail', 'nullable', 'string', 'max:100'],
            'location' => ['bail', 'nullable', 'string', 'max:100'],
            'employment_type' => ['bail', 'required', new Enum(EmploymentType::class)],
            'description' => ['bail', 'required', 'string', 'max:5000'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
