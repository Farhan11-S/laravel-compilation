<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;

class UpdateCategoryRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('category')->id;

        return [
            'name' => ['required', 'string', 'unique:categories,name,' . $categoryId . ',id'],
            'type' => ['required', 'string'],
        ];
    }
}
