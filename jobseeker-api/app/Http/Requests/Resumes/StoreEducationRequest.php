<?php

namespace App\Http\Requests\Resumes;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class StoreEducationRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'level' => ['required', 'string'],
            'field_of_study' => ['nullable', 'string'],
            'school_name' => ['required', 'string'],
            'country' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'is_currently_enrolled' => ['required', 'boolean'],
            'from' => ['required', 'date'],
            'to' => ['required_if:is_currently_enrolled,false', 'date', 'nullable'],
        ];
    }
}
