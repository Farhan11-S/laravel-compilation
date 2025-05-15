<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;

class StoreCategoryRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:categories'],
            'type' => ['required', 'string'],
        ];
    }
}
