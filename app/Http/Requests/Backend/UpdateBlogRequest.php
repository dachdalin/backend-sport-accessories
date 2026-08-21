<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'blog_category_id' => ['bail', 'nullable', 'integer', Rule::exists('blog_categories', 'id')],
            'title' => ['bail', 'required', 'string', 'max:150'],
            'writer' => ['bail', 'nullable', 'string', 'max:100'],
            'description' => ['bail', 'required', 'string'],
            'image' => ['bail', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_alt_text' => ['bail', 'nullable', 'string', 'max:255'],
            'is_published' => ['bail', 'sometimes', 'boolean'],
            'published_at' => ['bail', 'nullable', 'date'],
        ];
    }
}
