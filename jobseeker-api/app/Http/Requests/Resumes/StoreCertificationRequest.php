<?php

namespace App\Http\Requests\Resumes;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class StoreCertificationRequest extends LoggedInFormRequest
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
            'does_not_expire' => ['required', 'boolean'],
            'from' => ['required', 'date'],
            'to' => ['required_if:does_not_expire,false', 'date', 'nullable'],
            'description' => ['nullable', 'string'],
        ];
    }
}
