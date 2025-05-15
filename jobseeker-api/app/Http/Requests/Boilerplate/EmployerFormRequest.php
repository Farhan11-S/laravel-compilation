<?php

namespace App\Http\Requests\Boilerplate;

use Illuminate\Foundation\Http\FormRequest;

class EmployerFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->isEmployer();
    }
}
