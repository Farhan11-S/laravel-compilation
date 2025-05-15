<?php

namespace App\Http\Requests\SubscriptionLevel;

use App\Enums\PremiumAdPlace;
use App\Http\Requests\Boilerplate\SuperadminFormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateSubscriptionLevel extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'limit_create_job' => ['required', 'integer'],
            'limit_interview_schedules' => ['required', 'integer'],
            'unlimited_candidate_application' => ['required', 'boolean'],
            'show_resume_search_menu' => ['required', 'boolean'],
            'premium_ads' => ['required', 'array'],
            'premium_ads.*' => ['required', 'string', new Enum(PremiumAdPlace::class)],
        ];
    }
}
