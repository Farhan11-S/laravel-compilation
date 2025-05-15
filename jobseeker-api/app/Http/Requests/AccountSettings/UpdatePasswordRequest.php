<?php

namespace App\Http\Requests\AccountSettings;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UpdatePasswordRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $user = auth()->user();
        return [
            'password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }],
            'new_password' => ['required', 'confirmed', 'different:password', Rules\Password::defaults()],
        ];
    }
}
