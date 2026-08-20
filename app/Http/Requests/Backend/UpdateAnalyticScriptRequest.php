<?php

namespace App\Http\Requests\Backend;

use App\Enums\AnalyticScriptType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateAnalyticScriptRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:255', Rule::unique('analytic_scripts', 'name')->ignore($this->route('analytic_script'))],
            'type' => ['bail', 'required', new Enum(AnalyticScriptType::class)],
            'script_id' => ['bail', 'nullable', 'string', 'max:255'],
            'script' => ['bail', 'required', 'string'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
