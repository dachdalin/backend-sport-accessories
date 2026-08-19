<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHelpTopicRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['bail', 'sometimes', 'string', 'max:191'],
            'question' => ['bail', 'required', 'string', 'max:2000'],
            'answer' => ['bail', 'required', 'string', 'max:5000'],
            'ranking' => ['bail', 'sometimes', 'integer', 'min:1'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
