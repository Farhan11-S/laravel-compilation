<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (auth()->user()->company_id == $this->route('company')) || auth()->user()->isSuperadmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', Rule::unique('companies')->ignore($this->route('company')), 'string'],
            'office_address' => ['nullable', 'string'],
            'number_of_employee' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
            'industry' => ['nullable', 'string'],
            'sub_industry' => ['nullable', 'string'],
            'logo' => ['nullable'],
            'banner' => ['nullable'],
            'website_url' => ['nullable', 'string'],
        ];
    }
}
