<?php

namespace App\Http\Requests\Company;

use App\Constants\Roles;
use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        if ($user->role_id == Roles::JOB_SEEKER) {
            $user->role_id = Roles::EMPLOYER;
            $user->save();
        }
        return empty($user->company);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
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
