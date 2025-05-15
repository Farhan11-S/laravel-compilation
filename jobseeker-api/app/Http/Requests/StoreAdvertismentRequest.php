<?php

namespace App\Http\Requests;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;
use Illuminate\Validation\Rule;

class StoreAdvertismentRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'integer'],
            'is_code' => ['required', 'boolean'],
            'link' => Rule::requiredIf(fn () => !$this->is_code),
            'img' => Rule::requiredIf(fn () => !$this->is_code),
            'code' => Rule::requiredIf(fn () => $this->is_code),
        ];
    }
}
