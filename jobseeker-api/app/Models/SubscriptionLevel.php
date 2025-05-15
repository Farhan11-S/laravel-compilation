<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionLevel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'limit_create_job',
        'limit_interview_schedules',
        'unlimited_candidate_application',
        'show_resume_search_menu',
        'premium_ads',
    ];

    public function subscriptionItems()
    {
        return $this->hasMany(SubscriptionItem::class, 'level');
    }
}
