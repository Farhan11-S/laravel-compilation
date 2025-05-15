<?php

namespace App\Http\Requests\Blog;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class StoreBlogRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'sub_title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'tag_ids' => ['required', 'array'],
            'tag_ids.*' => ['required', 'integer', 'exists:tags,id'],
            'category_ids' => ['required', 'array'],
            'category_ids.*' => ['required', 'integer', 'exists:categories,id'],
            'thumbnail' => ['nullable'],
        ];
    }
}
