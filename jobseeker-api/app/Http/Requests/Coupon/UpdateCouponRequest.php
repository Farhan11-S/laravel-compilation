<?php

namespace App\Http\Requests\Coupon;

use App\Enums\CouponType;
use App\Http\Requests\Boilerplate\SuperadminFormRequest;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'duration' => ['required_if:type,' . CouponType::REGISTRATION->value, 'integer', 'nullable'],
            'description' => ['nullable', 'string'],
            'expired_at' => ['nullable', 'date'],
            'type' => ['required', Rule::enum(CouponType::class)],
            'value' => ['nullable']
        ];
    }
}
