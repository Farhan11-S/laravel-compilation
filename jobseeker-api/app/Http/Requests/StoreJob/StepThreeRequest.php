<?php

namespace App\Http\Requests\StoreJob;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class StepThreeRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'minimum_wage' => ['nullable', 'integer'],
            'maximum_wage' => ['nullable', 'integer', 'gte:minimum_wage'],
            'rate' => ['nullable', 'string'],
            'pay_by' => ['nullable', 'string'],
        ];
    }
}
