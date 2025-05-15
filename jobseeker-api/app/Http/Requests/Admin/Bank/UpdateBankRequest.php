<?php

namespace App\Http\Requests\Admin\Bank;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;

class UpdateBankRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'abbreviation' => ['required', 'string'],
            'name' => ['required', 'string'],
            'logo' => ['required'],
        ];
    }
}
