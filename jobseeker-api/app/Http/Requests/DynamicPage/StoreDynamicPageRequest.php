<?php

namespace App\Http\Requests\DynamicPage;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;
use App\Models\DynamicPage;

class StoreDynamicPageRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'link' => ['required', 'string', 'unique:' . DynamicPage::class],
            'content' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'parent' => ['required', 'string'],
        ];
    }
}
