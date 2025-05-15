<?php

namespace App\Http\Controllers;

use App\Http\Requests\InterviewSchedule\StoreInterviewScheduleRequest;
use App\Http\Requests\InterviewSchedule\UpdateInterviewScheduleRequest;
use App\Models\Candidate;
use App\Models\InterviewSchedule;
use App\Models\SubscriptionLevel;
use App\Notifications\InterviewReschedule;
use App\Notifications\InterviewSchedule as NotificationsInterviewSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterviewScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInterviewScheduleRequest $request)
    {
        $employer = auth()->user();
        $interviewPostedCount = InterviewSchedule::where('created_by', $employer->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        $maxInterviewPost = 5;

        if ($employer->isPremium()) {
            $sl = SubscriptionLevel::find($employer->level);
            if ($sl !== null && $sl->limit_interview_schedules > 5) {
                $maxInterviewPost = $sl->limit_interview_schedules;
            }
        }

        if ($interviewPostedCount >= $maxInterviewPost) {
            return response()->json([
                'success' => false,
                'message' => 'Anda telah mencapai batas maksimal interview per bulan!',
                'max_interview_post' => $maxInterviewPost,
                'error' => 'max-interview-post',
            ], 400);
        }

        $interviewSchedule = new InterviewSchedule();
        $validated = $request->validated();

        DB::transaction(function () use ($interviewSchedule, $validated) {

            $interviewSchedule->fill($validated);
            $interviewSchedule->created_by = auth()->user()->id;

            $interviewSchedule->save();

            $candidate = Candidate::find($validated['candidate_id']);
            unset($validated['candidate_id']);
            $candidate->interview_schedule_id = $interviewSchedule->id;
            $candidate->user->notify(new NotificationsInterviewSchedule($interviewSchedule->id));
            $candidate->save();
        });

        return $interviewSchedule;
    }

    /**
     * Display the specified resource.
     */
    public function show(InterviewSchedule $interviewSchedule)
    {
        $interviewSchedule->load([
            'created_by_user.company'
        ]);
        return [
            'success' => true,
            'message' => 'success',
            'data' => $interviewSchedule
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInterviewScheduleRequest $request, InterviewSchedule $interviewSchedule)
    {
        $user = auth()->user();
        $validated = $request->validated();
        $candidate = $interviewSchedule->candidate->user;
        $referencedNotification = $candidate->notifications->first(fn($value) => @$value->data['reference_type'] == InterviewSchedule::class && @$value->data['reference_id'] == $interviewSchedule->id);

        if ($user->id == $interviewSchedule->created_by) {
            $validated['status'] = null;
            $referencedNotification->read_at = null;
            $referencedNotification->save();
        } elseif ($user->id == $interviewSchedule->candidate->user_id) {
            $referencedNotification->markAsRead();

            $interviewSchedule->created_by_user->notify(new InterviewReschedule($interviewSchedule->candidate->id));
        } else {
            abort(403);
        }
        $interviewSchedule->update($validated);

        return [
            'success' => true,
            'message' => 'success',
            'data' => $interviewSchedule
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InterviewSchedule $interviewSchedule)
    {
        //
    }
}
