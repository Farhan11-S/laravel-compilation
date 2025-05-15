<?php

namespace App\Http\Requests\InterviewSchedule;

use App\Enums\InterviewSchedules\InterviewScheduleStatus;
use App\Enums\InterviewSchedules\InterviewScheduleTypes;
use App\Rules\LatitudeRule;
use App\Rules\LongitudeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateInterviewScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(InterviewScheduleTypes::class)],
            'start' => ['required', 'date_format:Y-m-d H:i:s'],
            'end' => ['nullable', 'date_format:Y-m-d H:i:s', 'after:start'],
            'link' => ['required_if:type,' . InterviewScheduleTypes::ONLINE->value, 'string', 'nullable'],
            'longitude' => ['required_if:type,' . InterviewScheduleTypes::OFFLINE->value, new LongitudeRule, 'nullable'],
            'latitude' => ['required_if:type,' . InterviewScheduleTypes::OFFLINE->value, new LatitudeRule, 'nullable'],
            'pic' => ['required_if:type,' . InterviewScheduleTypes::OFFLINE->value, 'string', 'nullable'],
            'reschedule_request_datetime' => ['nullable', 'date_format:Y-m-d H:i:s', 'after:today'],
            'reschedule_reasoning' => ['nullable', 'string'],
            'status' => ['required', new Enum(InterviewScheduleStatus::class)],
        ];
    }
}
