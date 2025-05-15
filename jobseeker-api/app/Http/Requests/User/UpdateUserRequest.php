<?php

namespace App\Http\Requests\User;

use App\Constants\Roles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['nullable', 'string', 'max:255', 'unique:' . User::class],
            'role_id' => ['nullable', 'integer', 'exists:' . Role::class . ',id'],
            'company_name' => ['required_if:role_id,' . Roles::EMPLOYER, 'string'],
            'company_industry' => ['nullable', 'string'],
        ];
    }
}
