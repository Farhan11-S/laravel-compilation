<?php

namespace App\Http\Requests\AccountSettings;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;
use App\Models\User;

class UpdatePhoneRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:255', 'unique:' . User::class],
        ];
    }
}
