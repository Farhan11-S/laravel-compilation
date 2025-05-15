<?php

namespace App\Http\Requests\Job;

use App\Enums\ResumeRequired;
use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $jobId = $this->route('job');

        return Job::where('user_id', auth()->user()->id)->where('id', $jobId)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'country' => ['required', 'string'],
            'language' => ['required', 'string'],
            'job_title' => ['required', 'string'],
            'location' => ['required', 'string'],
            'int_hires_needed' => ['nullable', 'integer'],
            'job_type' => ['nullable'],
            'hours_per_week' => ['nullable', 'string'],
            'contract_length' => ['nullable', 'string'],
            'contract_period' => ['nullable', 'string'],
            'minimum_wage' => ['nullable', 'integer'],
            'maximum_wage' => ['nullable', 'integer', 'gte:minimum_wage'],
            'rate' => ['nullable', 'string'],
            'currency_code' => ['nullable', 'string'],
            'job_description' => ['required', 'string'],
            'communication_email' => ['nullable', 'array'],
            'communication_email.*' => ['required', 'string', 'email'],
            'expected_hire_date' => ['nullable', 'integer'],
            'resume_required' => ['nullable', 'string', new Enum(ResumeRequired::class)],
            'application_deadline' => ['nullable', 'date'],
            'application_deadline' => ['nullable', 'date'],
            'auto_reject_candidate' => ['nullable', 'boolean'],
            'job_status' => ['nullable', 'string'],
            'can_message' => ['nullable', 'boolean'],
            'email_subject_format' => ['nullable', 'string'],
        ];
    }
}
