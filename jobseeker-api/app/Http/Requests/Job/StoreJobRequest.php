<?php

namespace App\Http\Requests\Job;

use App\Enums\ResumeRequired;
use App\Http\Requests\Boilerplate\EmployerFormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreJobRequest extends EmployerFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'country' => ['required', 'string'],
            'language' => ['required', 'string'],
            'job_title' => ['required', 'string'],
            'location' => ['required', 'string'],
            'int_hires_needed' => ['nullable', 'integer'],
            'job_type' => ['required', 'array'],
            'job_type.*' => ['required', 'string'],
            'hours_per_week' => ['nullable', 'string'],
            'contract_length' => ['nullable', 'string'],
            'contract_period' => ['nullable', 'string'],
            'minimum_wage' => ['nullable', 'integer'],
            'maximum_wage' => ['nullable', 'integer', 'gte:minimum_wage'],
            'rate' => ['nullable', 'string'],
            'currency_code' => ['nullable', 'string'],
            'job_description' => ['required', 'string'],
            'communication_email' => ['required', 'array'],
            'communication_email.*' => ['required', 'string', 'email'],
            'expected_hire_date' => ['nullable', 'integer'],
            'resume_required' => ['required', 'string', new Enum(ResumeRequired::class)],
            'application_deadline' => ['nullable', 'date'],
            'auto_reject_candidate' => ['required', 'boolean'],
            'email_subject_format' => ['nullable', 'string'],
        ];

        return $rules;
    }
}
