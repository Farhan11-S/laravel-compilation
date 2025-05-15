<?php

namespace App\Http\Requests\StoreJob;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;
use Illuminate\Validation\Rule;

class StepOneRequest extends LoggedInFormRequest
{
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
            'user_id' => [Rule::requiredIf(auth()->user()->isSuperadmin())],
            'external_apply_link' => ['nullable', 'string', 'url:http,https']
        ];
    }
}
