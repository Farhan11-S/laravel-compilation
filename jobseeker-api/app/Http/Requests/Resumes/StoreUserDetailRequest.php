<?php

namespace App\Http\Requests\Resumes;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class StoreUserDetailRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_detail.first_name' => ['required', 'string'],
            'user_detail.last_name' => ['required', 'string'],
            'user_detail.country' => ['required', 'string'],
            'user_detail.province' => ['nullable', 'string'],
            'user_detail.street_address' => ['nullable', 'string'],
            'user_detail.city' => ['required', 'string'],
            'user_detail.postal_code' => ['nullable', 'string'],
            'user_detail.social_medias' => ['nullable', 'array'],
            'user_detail.bio' => ['nullable', 'string'],
            'user_detail.date_of_birth' => ['nullable', 'date'],
            'user_detail.place_of_birth' => ['nullable', 'string'],
        ];
    }
}
