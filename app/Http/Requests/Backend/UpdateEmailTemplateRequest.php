<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmailTemplateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:150', Rule::unique('email_templates', 'name')->ignore($this->route('email_template'))],
            'subject' => ['bail', 'required', 'string', 'max:191'],
            'body' => ['bail', 'required', 'string'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
