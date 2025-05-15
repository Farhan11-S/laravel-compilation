<?php

namespace App\Http\Requests;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;

class UpdateEmailTemplateRequest extends SuperadminFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'greeting' => ['required', 'string'],
            'lines' => ['required', 'array'],
            'lines.*' => ['required', 'string'],
            'salutation' => ['required', 'string'],
        ];
    }
}
