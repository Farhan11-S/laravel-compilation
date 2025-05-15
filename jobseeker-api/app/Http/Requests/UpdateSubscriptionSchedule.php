<?php

namespace App\Http\Requests;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;

class UpdateSubscriptionSchedule extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'interval' => ['required', 'string'],
            'interval_count' => ['required', 'integer'],
            'total_recurrence' => ['nullable', 'integer'],
            'retry_interval' => ['nullable', 'string'],
            'retry_interval_count' => ['nullable', 'integer'],
            'total_retry' => ['nullable', 'integer'],
            'failed_attempt_notifications' => ['nullable', 'array'],
        ];
    }
}
