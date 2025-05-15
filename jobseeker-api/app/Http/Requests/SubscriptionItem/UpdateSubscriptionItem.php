<?php

namespace App\Http\Requests\SubscriptionItem;

use App\Enums\CouponType;
use App\Http\Requests\Boilerplate\SuperadminFormRequest;
use Illuminate\Validation\Rule;

class UpdateSubscriptionItem extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'net_unit_amount' => ['required', 'integer'],
            'type' => ['required', 'string'],
            'subcategory' => ['required', 'string'],
            'schedule_id' => ['required', 'integer'],
            'features' => ['nullable', 'array'],
            'role_id' => ['required', 'integer'],
            'color' => ['nullable', 'string'],
            'coupon_id' => ['nullable', Rule::exists('coupons', 'id')->where(function ($query) {
                return $query->whereIn('type', [
                    CouponType::DISCOUNT_COUPON_EMPLOYER,
                    CouponType::DISCOUNT_COUPON_JOBSEEKER,
                ]);
            })],
            'currency_amounts' => ['required', 'array'],
            'currency_amounts.*.currency_code' => ['required', 'string'],
            'currency_amounts.*.amount' => ['required', 'integer'],
        ];
    }
}
