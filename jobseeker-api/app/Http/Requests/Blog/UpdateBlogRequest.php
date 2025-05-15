<?php

namespace App\Http\Requests\Blog;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class UpdateBlogRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string'],
            'sub_title' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['nullable', 'integer', 'exists:tags,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['nullable', 'integer', 'exists:categories,id'],
            'thumbnail' => ['nullable'],
        ];
    }
}
