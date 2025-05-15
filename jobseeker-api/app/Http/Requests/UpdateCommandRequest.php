<?php

namespace App\Http\Requests;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommandRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'value' => ['required', 'string', 'max:255', new \App\Rules\CronExpression()],
            'limit' => ['required', 'integer', 'min:1'],
        ];
    }
}
