<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionCurrencyAmount extends Model
{
    use HasFactory;

    protected $table = 'subscription_currency_amount';

    protected $fillable = [
        'subscription_item_id',
        'currency_code',
        'amount',
    ];

    public function subscriptionItem()
    {
        return $this->belongsTo(SubscriptionItem::class);
    }
}
