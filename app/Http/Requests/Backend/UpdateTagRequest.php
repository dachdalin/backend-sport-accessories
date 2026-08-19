<?php

namespace App\Http\Requests\Backend;

use App\Services\Tags\TagService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateTagRequest extends FormRequest
{
    public function __construct(private readonly TagService $tags)
    {
        parent::__construct();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tag' => ['bail', 'required', 'string', 'max:191', 'regex:/^[\pL\pN\s\-]+$/u'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (! is_string($this->input('tag')) || $this->input('tag') === '') {
                    return;
                }

                $normalized = $this->tags->normalize($this->string('tag')->value());
                $ignoreId = $this->route('tag')?->id;

                if ($this->tags->findDuplicate($normalized, $ignoreId) !== null) {
                    $validator->errors()->add('tag', __('This tag already exists.'));
                }
            },
        ];
    }
}
