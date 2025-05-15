<?php

namespace App\Http\Requests\Resumes;

use App\Enums\LanguageSkillLevel;
use App\Models\Resume;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateResumeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $resumeId = $this->route('resume')->id;

        return Resume::where('user_id', auth()->user()->id)->where('id', $resumeId)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'is_shared' => ['nullable', 'boolean'],
            'cv_template' => ['nullable', 'string'],
            'certifications' => ['required', 'array'],
            'educations' => ['required', 'array'],
            'skills' => ['required', 'array'],
            'work_experiences' => ['required', 'array'],
            'language_skills' => ['required', 'array'],
            'certifications.*.id' => ['nullable', 'integer'],
            'certifications.*.name' => ['required', 'string'],
            'certifications.*.does_not_expire' => ['required', 'boolean'],
            'certifications.*.from' => ['required', 'date'],
            'certifications.*.to' => ['required_if:does_not_expire,false', 'date', 'nullable'],
            'certifications.*.description' => ['nullable', 'string'],
            'educations.*.id' => ['nullable', 'integer'],
            'educations.*.level' => ['required', 'string'],
            'educations.*.field_of_study' => ['nullable', 'string'],
            'educations.*.school_name' => ['required', 'string'],
            'educations.*.country' => ['nullable', 'string'],
            'educations.*.city' => ['nullable', 'string'],
            'educations.*.is_currently_enrolled' => ['required', 'boolean'],
            'educations.*.from' => ['required', 'date'],
            'educations.*.to' => ['required_if:is_currently_enrolled,false', 'date', 'nullable'],
            'skills.*.id' => ['nullable', 'integer'],
            'skills.*.name' => ['required', 'string'],
            'skills.*.years_of_experience' => ['required', 'integer'],
            'language_skills.*.id' => ['nullable', 'integer'],
            'language_skills.*.name' => ['required', 'string'],
            'language_skills.*.level' => ['required', 'string', new Enum(LanguageSkillLevel::class)],
            'work_experiences.*.id' => ['nullable', 'integer'],
            'work_experiences.*.job_title' => ['required', 'string'],
            'work_experiences.*.company' => ['nullable', 'string'],
            'work_experiences.*.city' => ['nullable', 'string'],
            'work_experiences.*.country' => ['nullable', 'string'],
            'work_experiences.*.is_currently_work_here' => ['required', 'boolean'],
            'work_experiences.*.from' => ['required', 'date'],
            'work_experiences.*.to' => ['required_if:is_currently_work_here,false', 'date', 'nullable'],
            'work_experiences.*.description' => ['nullable', 'string'],
            'user_detail.first_name' => ['required', 'string'],
            'user_detail.last_name' => ['required', 'string'],
            'user_detail.country' => ['required', 'string'],
            'user_detail.province' => ['nullable', 'string'],
            'user_detail.street_address' => ['nullable', 'string'],
            'user_detail.city' => ['required', 'string'],
            'user_detail.postal_code' => ['nullable', 'string'],
            'user_detail.social_medias' => ['nullable', 'array'],
            'user_detail.bio' => ['nullable', 'string'],
            'user_detail.date_of_birth' => ['nullable', 'date'],
            'user_detail.place_of_birth' => ['nullable', 'string'],
            'user.phone' => ['nullable', 'string'],
        ];
    }
}
