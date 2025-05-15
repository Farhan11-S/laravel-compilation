<?php

namespace App\Http\Requests\StoreJob;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class StepFourRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'job_description' => ['required', 'string'],
        ];
    }
}
