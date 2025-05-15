<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'name',
        'net_unit_amount',
        'subcategory',
        'schedule_id',
        'features',
        'role_id',
        'level',
        'color',
        'coupon_id',
        'premium_job_duration'
    ];

    protected $casts = [
        'features' => 'array',
    ];

    protected $with = ['currencyAmounts'];

    public function subscriptionSchedule(): BelongsTo
    {
        return $this->belongsTo(SubscriptionSchedule::class, 'schedule_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function subscriptionLevel(): BelongsTo
    {
        return $this->belongsTo(SubscriptionLevel::class, 'level');
    }

    public function currencyAmounts(): HasMany
    {
        return $this->hasMany(SubscriptionCurrencyAmount::class);
    }
}
