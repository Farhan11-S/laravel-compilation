<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionSchedule extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'failed_attempt_notifications' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'interval',
        'interval_count',
        'total_recurrence',
        'retry_interval',
        'retry_interval_count',
        'total_retry',
        'failed_attempt_notifications',
        'schedule_id',
    ];

    public function subscription_items(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class);
    }
}
