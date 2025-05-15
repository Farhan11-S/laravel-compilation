<?php

namespace App\Http\Controllers;

use App\Enums\CandidateStatus;
use App\Models\Candidate;

class JobSeekerController extends Controller
{
    public function userAppliedCheck(string $job_id)
    {
        return [
            'candidate' => Candidate::where('user_id', auth()->user()->id)
                ->where('job_id', $job_id)
                ->first()
        ];
    }

    public function myJobList()
    {
        $data = Candidate::with(['job', 'job.user', 'job.user.company', 'document_request', 'test_assesment'])
            ->where('user_id', auth()->user()->id)
            ->whereHas('job')
            ->get();

        $applied = $data
            ->whereNotIn('status', [CandidateStatus::SAVED->value]);

        $saved = $data
            ->where('status', CandidateStatus::SAVED->value)
            ->flatten();

        $accepted = $data
            ->where('status', CandidateStatus::ACCEPTED->value)
            ->flatten();

        $rejected = $data
            ->where('status', CandidateStatus::REJECTED->value)
            ->flatten();

        return [
            "applied" => $applied->all(),
            "saved" => $saved->all(),
            "accepted" => $accepted->all(),
            "rejected" => $rejected->all(),
        ];
    }
}
