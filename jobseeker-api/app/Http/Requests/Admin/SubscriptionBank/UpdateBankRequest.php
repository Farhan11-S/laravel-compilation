<?php

namespace App\Http\Requests\Admin\SubscriptionBank;

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
            'bank_id' => ['required', 'integer', 'exists:banks,id'],
            'account_number' => ['required', 'string'],
            'account_name' => ['required', 'string'],
        ];
    }
}
