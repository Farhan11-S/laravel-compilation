<?php

namespace App\Http\Requests\Boilerplate;

use Illuminate\Foundation\Http\FormRequest;

class LoggedInFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }
}
