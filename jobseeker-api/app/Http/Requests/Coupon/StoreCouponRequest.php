<?php

namespace App\Http\Requests\Coupon;

use App\Enums\CouponType;
use App\Http\Requests\Boilerplate\SuperadminFormRequest;
use App\Models\Coupon;
use Illuminate\Validation\Rule;

class StoreCouponRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'unique:' . Coupon::class],
            'duration' => ['required_if:type,' . CouponType::REGISTRATION->value, 'integer', 'nullable'],
            'description' => ['nullable', 'string'],
            'expired_at' => ['nullable', 'date'],
            'type' => ['required', Rule::enum(CouponType::class)],
            'value' => ['nullable']
        ];
    }
}
