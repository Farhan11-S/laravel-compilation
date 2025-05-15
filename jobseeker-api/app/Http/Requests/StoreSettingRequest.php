<?php

namespace App\Http\Requests;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;

class StoreSettingRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'value' => ['required'],
            'is_image' => ['required', 'boolean'],
        ];
    }
}
