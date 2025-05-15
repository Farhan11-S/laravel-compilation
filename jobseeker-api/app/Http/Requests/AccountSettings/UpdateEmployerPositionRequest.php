<?php

namespace App\Http\Requests\AccountSettings;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class UpdateEmployerPositionRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'position' => ['required', 'string', 'max:255'],
        ];
    }
}
