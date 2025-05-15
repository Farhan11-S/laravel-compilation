<?php

namespace App\Http\Requests\AccountSettings;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateEmailRequest extends LoggedInFormRequest
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
            'new_email' => ['required', 'string', 'email', 'unique:' . User::class . ',email', function ($attribute, $value, $fail) use ($user) {
                if ($user->email == $value) {
                    return $fail(__('The new email must be different from current email'));
                }
            }],
        ];
    }
}
