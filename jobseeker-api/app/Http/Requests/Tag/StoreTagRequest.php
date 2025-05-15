<?php

namespace App\Http\Requests\Tag;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class StoreTagRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:tags'],
            'type' => ['required', 'string'],
        ];
    }
}
