<?php

namespace App\Http\Requests\Tag;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class UpdateTagRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $tagId = $this->route('tag')->id;

        return [
            'name' => ['required', 'string', 'unique:tags,name,' . $tagId . ',id'],
            'type' => ['required', 'string'],
        ];
    }
}
