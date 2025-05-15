<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InterviewSchedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'longitude',
        'latitude',
        'pic',
        'link',
        'start',
        'end',
        'reschedule_request_datetime',
        'reschedule_reasoning',
        'created_by',
        'status',
    ];

    public function created_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function candidate(): HasOne
    {
        return $this->hasOne(Candidate::class)->latestOfMany();
    }
}
