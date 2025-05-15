<?php

namespace App\Http\Requests\StoreJob;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class StepTwoRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'job_type' => ['required', 'array'],
            'job_type.*' => ['required', 'string'],
            'hours_per_week' => ['nullable', 'string'],
            'contract_length' => ['nullable', 'string'],
            'contract_period' => ['nullable', 'string'],
        ];

        return $rules;
    }
}
