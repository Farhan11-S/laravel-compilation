<?php

namespace App\Http\Requests\AccountSettings;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'role_id' => ['required', 'integer', 'min:2', 'max:3']
        ];
    }
}
