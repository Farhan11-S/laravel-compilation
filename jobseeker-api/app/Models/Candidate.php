<?php

namespace App\Models;

use App\Enums\CandidateAttachmentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'job_id',
        'status',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function interview_schedule(): BelongsTo
    {
        return $this->belongsTo(InterviewSchedule::class);
    }

    public function document_request()
    {
        return $this->hasOne(CandidateAttachment::class)
            ->where('content_type', CandidateAttachmentType::REQUEST_ADDITIONAL_DOCUMENTS)
            ->oldest();
    }

    public function test_assesment()
    {
        return $this->hasOne(CandidateAttachment::class)
            ->where('content_type', CandidateAttachmentType::ASSESMENT_TEST)
            ->oldest();
    }
}
