<?php

namespace App\Http\Requests\User;

use App\Constants\Roles;
use App\Http\Requests\Boilerplate\SuperadminFormRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rules;

class StoreUserRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['nullable', 'string', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'integer', 'exists:' . Role::class . ',id'],
            'company_name' => ['required_if:role_id,' . Roles::EMPLOYER, 'string'],
            'company_industry' => ['nullable', 'string'],
        ];
    }
}
