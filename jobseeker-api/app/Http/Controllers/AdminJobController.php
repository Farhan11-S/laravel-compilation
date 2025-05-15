<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Http\Requests\Admin\ListJob\StoreJobRequest;
use App\Http\Requests\Admin\ListJob\UpdateJobRequest;
use App\Models\Job;
use App\Models\SubscriptionItem;
use App\Models\User;
use App\Notifications\JobCreatedNotification;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminJobController extends Controller
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
        $sort_by = $query['sort_by'] ?? 'updated_at';
        $order = $query['order'] ?? 'desc';
        $trashed = $query['trashed'] ?? false;
        $draft = $query['draft'] ?? false;
        $company_id = $query['company_id'] ?? '';
        $expired = $query['expired'] ?? false;
        $filterPreset = $query['filter_preset'] ?? '';

        $query = Job::when($trashed, fn($query) => $query->onlyTrashed())
            ->when(
                $draft,
                function (Builder $query) {
                    $query->whereDate('published_at', '>', now());
                },
                function (Builder $query) {
                    $query->where(function (Builder $query) {
                        $query->whereDate('published_at', '<=', now())
                            ->orWhereNull('published_at');
                    });
                },
            )
            ->when(
                $what,
                fn(Builder $query, string $what) =>
                $query->where(function (Builder $query) use ($what) {
                    $query->where(DB::raw('lower(job_title)'), 'like', '%' . strtolower($what) . '%')
                        ->orWhereHas(
                            'user',
                            fn(Builder $query) =>
                            $query->where(function (Builder $query) use ($what) {
                                $query->where(DB::raw('lower(name)'), 'like', '%' . strtolower($what) . '%')
                                    ->orWhere(DB::raw('lower(email)'), 'like', '%' . strtolower($what) . '%');
                            })
                        );
                })
            )
            ->when(
                $where,
                fn(Builder $query, string $where) => $query->where(DB::raw('lower(location)'), 'like', '%' . strtolower($where) . '%')
            )
            ->when($company_id, fn($query) => $query->where('company_id', $company_id))
            ->when(
                $expired,
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
            ->with(['user', 'company', 'deletedBy', 'jobPremium'])
            ->orderByRaw($sort_by . ' ' . $order);

        Helper::filterPreset($query, $filterPreset);

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
        $assignedUser = User::findOrFail($request['user_id']);
        $job = new Job();

        $validated = $request->validated();

        $convertDateToTime = null;
        if (!empty($validated['application_deadline'])) $convertDateToTime = Helper::utcstrtotime($validated['application_deadline']);

        if ($convertDateToTime) $validated['application_deadline'] = date('Y-m-d', $convertDateToTime);

        $validated['company_id'] = $assignedUser->company_id;
        $job->fill($validated);
        $job->is_hiring_manager = true;

        $job->save();

        $assignedUser->notify(new JobCreatedNotification());

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
        return Job::withCount([
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
            ->with(['user', 'company'])
            ->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequest $request, Job $job)
    {
        $validated = $request->validated();

        $convertDateToTime = null;
        if (!empty($validated['application_deadline'])) $convertDateToTime = Helper::utcstrtotime($validated['application_deadline']);

        if ($convertDateToTime) $validated['application_deadline'] = date('Y-m-d', $convertDateToTime);

        $job->update($validated);

        return [
            'success' => true,
            'message' => 'Berhasil merubah detail pekerjaan!'
        ];
    }

    /**
     * Toggle job premium
     */
    public function toggleJobPremium(string $id)
    {
        $job = Job::findOrFail($id);
        $employer = $job->user;
        $jobPremiumDuration = 3;

        if ($employer->isPremium()) {
            $subsItem = SubscriptionItem::find($employer->package_type);

            if ($subsItem) {
                $jobPremiumDuration = $subsItem->premium_job_duration;
            }
        }

        if ($job->jobPremium) {
            $job->jobPremium->delete();
        } else {
            $job->jobTags()->create([
                'tag_name' => 'HOT',
                'color_hex' => '#fb923c',
                'ended_at' => now()->addDays($jobPremiumDuration),
            ]);
        }

        $job->load('jobPremium');

        return $job;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        //
    }
}
