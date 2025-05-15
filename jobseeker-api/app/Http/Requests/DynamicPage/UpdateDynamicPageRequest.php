<?php

namespace App\Http\Requests\DynamicPage;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;
class UpdateDynamicPageRequest extends SuperadminFormRequest
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
            'content' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'parent' => ['required', 'string'],
        ];
    }
}
