<?php

namespace App\Http\Requests\Menu;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;

class UpdateMenuRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'duration' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
            'expired_at' => ['nullable', 'date'],
        ];
    }
}
