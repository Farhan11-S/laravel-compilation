<?php

namespace App\Http\Requests\Notification;

use App\Http\Requests\Boilerplate\EmployerFormRequest;

class StoreNotificationRequest extends EmployerFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|string',
            'notifiable_id' => 'required|integer',
            'created_by' => 'required|integer',
            'data' => 'required',
            'data.subject' => 'required_if:type,subject-message-notification|string',
            'data.message' => 'required_if:type,subject-message-notification|string',
        ];
    }
}
