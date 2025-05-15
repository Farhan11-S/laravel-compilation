<?php

namespace App\Http\Controllers;

use App\Http\Requests\Job\StoreJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Models\Job;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helper\Helper;
use App\Models\Setting;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $limit = $query['limit'] ?? 0;
        $what = $query['what'] ?? '';
        $where = $query['where'] ?? '';
        $sort_by = $query['sort_by'] ?? 'created_at';
        $order = $query['order'] ?? 'asc';
        $trashed = $query['trashed'] ?? false;
        $closed = $query['closed'] ?? false;

        $query = Job::where('user_id', auth()->user()->id)
            ->when(
                $what,
                function (Builder $query, string $what) {
                    $query->where(DB::raw('lower(job_title)'), 'like', '%' . strtolower($what) . '%');
                }
            )
            ->when(
                $where,
                function (Builder $query, string $where) {
                    $query->where(DB::raw('lower(location)'), 'like', '%' . strtolower($where) . '%');
                }
            )
            ->when(
                $trashed,
                function (Builder $query) {
                    $query->onlyTrashed();
                }
            )
            ->when(
                $closed,
                function (Builder $query) {
                    $query->whereDate('application_deadline', '<=', now());
                },
                function (Builder $query) {
                    $query->where(function (Builder $query) {
                        $query->whereDate('application_deadline', '>', now())
                            ->orWhereNull('application_deadline');
                    });
                },
            )
            ->withCount([
                'candidates as total_candidates_count' => function (Builder $query) {
                    $query->whereNot('status', 'saved');
                },
            ])
            ->with(['user', 'deletedBy'])
            ->orderByRaw($sort_by . ' ' . $order);

        $result = $query->get();

        if ($limit > 0) {
            $result = $query->paginate($limit);
        }

        return $result;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request)
    {
        $job = new Job();
        $employer = auth()->user();

        // $maxJobPostFrequency = Setting::where('name', 'max-job-post-frequency')->first()->value ?? 1;
        $jobPostedCount = Job::where('user_id', $employer->id)->count();
        $maxJobPost = Setting::where('name', 'max-job-post')->first()->value ?? 5;
        if ($employer->isPremium()) {
            $sl = SubscriptionLevel::find($employer->level);
            if ($sl !== null && $sl->limit_create_job > 5) {
                $maxJobPost = $sl->limit_create_job;
            }
        }

        if ($jobPostedCount >= $maxJobPost) {
            return response()->json([
                'success' => false,
                'message' => 'Anda telah mencapai batas maksimal posting pekerjaan!',
                'max_job_post' => $maxJobPost,
                'error' => 'max-job-post',
            ], 400);
        }

        $validated = $request->validated();

        $convertDateToTime = null;
        if (!empty($validated['application_deadline'])) $convertDateToTime = Helper::utcstrtotime($validated['application_deadline']);

        if ($convertDateToTime) $validated['application_deadline'] = date('Y-m-d', $convertDateToTime);

        $job->fill($validated);
        $job->is_hiring_manager = true;
        $job->user_id = $employer->id;
        $job->company_id = $employer->company_id;

        $job->save();

        return [
            'data' => $job,
            'success' => true,
            'message' => 'Pekerjaan telah berhasil ditambahkan!'
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $job = Job::where('user_id', auth()->user()->id)
            ->where('id', $id)
            ->withCount([
                'candidates' => function (Builder $query) {
                    $query->whereNot('status', 'saved');
                },
                'candidates as candidates_waiting_review_count' => function (Builder $query) {
                    $query->where('status', 'waiting_review');
                },
                'candidates as candidates_reviewed_count' => function (Builder $query) {
                    $query->where('status', 'reviewed');
                },
                'candidates as candidates_accepted_count' => function (Builder $query) {
                    $query->where('status', 'accepted');
                },
                'candidates as candidates_rejected_count' => function (Builder $query) {
                    $query->where('status', 'rejected');
                },
            ])
            ->firstOrFail();


        return $job;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Job::where('user_id', auth()->user()->id)->where('id', $id)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequest $request, string $id)
    {
        $validated = $request->validated();

        $convertDateToTime = null;
        if (!empty($validated['application_deadline'])) $convertDateToTime = Helper::utcstrtotime($validated['application_deadline']);

        if ($convertDateToTime) $validated['application_deadline'] = date('Y-m-d', $convertDateToTime);


        Job::where('user_id', auth()->user()->id)->where('id', $id)->update($validated);

        return [
            'success' => true,
            'message' => 'Berhasil merubah detail pekerjaan!'
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
