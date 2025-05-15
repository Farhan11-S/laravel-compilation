<?php

namespace App\Http\Requests\Menu;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;
use App\Models\Menu;

class StoreMenuRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'label' => ['required', 'string'],
            'link' => ['required', 'string', 'unique:' . Menu::class],
            'parent' => ['nullable', 'integer'],
            'position' => ['nullable', 'integer'],
        ];
    }
}
