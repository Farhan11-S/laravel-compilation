<?php

namespace App\Http\Requests\SubscriptionItem;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;

class StoreSubscriptionItem extends SuperadminFormRequest
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
            'features' => ['required', 'array'],
            'role_id' => ['required', 'integer'],
            'color' => ['nullable', 'string'],
        ];
    }
}
