<?php

namespace App\Http\Requests\Resumes;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkExperienceRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'job_title' => ['required', 'string'],
            'company' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'is_currently_work_here' => ['required', 'boolean'],
            'from' => ['required', 'date'],
            'to' => ['required_if:is_currently_work_here,false', 'date', 'nullable'],
            'description' => ['nullable', 'string'],
        ];
    }
}
