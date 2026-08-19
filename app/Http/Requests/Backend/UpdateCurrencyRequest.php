<?php

namespace App\Http\Requests\Backend;

use App\Services\CurrencyService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCurrencyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * Normalizes the code casing first so the uniqueness check matches
     * what will actually be stored.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(CurrencyService $currencies): array
    {
        $this->merge($currencies->normalize($this->only('code')));

        return [
            'name' => ['bail', 'required', 'string', 'max:191'],
            'symbol' => ['bail', 'required', 'string', 'max:10'],
            'code' => ['bail', 'required', 'string', 'max:10', Rule::unique('currencies', 'code')->ignore($this->route('currency'))],
            'exchange_rate' => ['bail', 'required', 'numeric', 'min:0.0001'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
