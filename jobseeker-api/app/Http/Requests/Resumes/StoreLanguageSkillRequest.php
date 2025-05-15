<?php

namespace App\Http\Requests\Resumes;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class StoreLanguageSkillRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'level' => ['required', 'string'],
        ];
    }
}
