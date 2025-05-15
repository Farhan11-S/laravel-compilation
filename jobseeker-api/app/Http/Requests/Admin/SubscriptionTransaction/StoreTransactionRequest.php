<?php

namespace App\Http\Requests\Admin\SubscriptionTransaction;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'subscription_item_id' => ['required', 'integer', 'exists:subscription_items,id'],
            'subscription_bank_id' => ['required', 'integer', 'exists:subscription_banks,id'],
            'account_number' => ['required', 'string'],
            'account_name' => ['required', 'string'],
            'coupon_id' => ['nullable', Rule::exists('coupons', 'id')->where(function (Builder $query) {
                return $query->whereIn('type', [
                    CouponType::DISCOUNT_COUPON_EMPLOYER,
                    CouponType::DISCOUNT_COUPON_JOBSEEKER,
                ]);
            })],
        ];
    }
}
