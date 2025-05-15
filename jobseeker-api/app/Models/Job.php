<?php

namespace App\Models;

use App\Traits\UseDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes, UseDeletedBy;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'communication_email' => 'array',
        'cc_emails' => 'array',
        'job_type' => 'array',
        'is_walk_in_interview' => 'boolean',
        'should_post' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'country',
        'language',
        'is_hiring_manager',
        'job_title',
        'location',
        'job_type',
        'int_hires_needed',
        'expected_hire_date',
        'minimum_wage',
        'maximum_wage',
        'rate',
        'currency_code',
        'job_description',
        'resume_required',
        'communication_email',
        'cc_emails',
        'application_deadline',
        'user_id',
        'company_id',
        'email_subject_format',
        'external_apply_link',
        'is_walk_in_interview',
        'should_post',
        'published_at',
        'deleted_at',
    ];

    protected $appends = [
        'is_job_expired',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function jobTags(): HasMany
    {
        return $this->hasMany(JobTag::class);
    }

    public function getIsJobExpiredAttribute(): bool
    {
        $is_job_expired = !empty($this->application_deadline) && now()->gt($this->application_deadline);

        if (
            $this
            ->user()
            ->whereHas(
                'subscriberJob',
                fn($query) => $query->where('status', 'active')
            )->doesntExist()
        ) {
            $is_job_expired = true;
        }

        return $is_job_expired;
    }

    public function jobPremium(): HasOne
    {
        $builder = $this->jobTags()
            ->whereDate('ended_at', '>', now())
            ->whereDate('started_at', '<=', now())
            ->where('tag_name', 'HOT');

        $relation = new HasOne($builder->getQuery(), $this, 'job_id', 'id');

        return $relation;
    }
}
