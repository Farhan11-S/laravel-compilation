<?php

namespace App\Http\Requests\Admin\Bank;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;
use App\Models\Bank;

class StoreBankRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'abbreviation' => ['required', 'string', 'unique:' . Bank::class],
            'name' => ['required', 'string'],
            'logo' => ['required'],
        ];
    }
}
