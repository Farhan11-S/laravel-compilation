<?php

namespace App\Http\Requests\Resumes;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class StoreSkillRequest extends LoggedInFormRequest
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
            'years_of_experience' => ['required', 'integer'],
        ];
    }
}
