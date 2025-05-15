<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Http\Requests\StoreCandidateRequest;
use App\Models\Candidate;
use App\Models\Job;
use App\Models\Setting;
use App\Notifications\CandidateStatusUpdated;
use App\Notifications\PostApplyNotification;
use App\Notifications\UserApplied;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $limit = $query['limit'] ?? 10;
        $jobID = $query['filter_by_job_id'] ?? null;
        $filterPreset = $query['filter_preset'] ?? null;
        $search = $query['search'] ?? '';
        $order = $query['order'] ?? 'asc';
        $jobseeker_name = $query['jobseeker_name'] ?? '';
        $company_name = $query['company_name'] ?? '';
        $created_at = $query['created_at'] ?? '';
        $status = $query['status'] ?? '';

        $canAccessAll = auth()->user()->can('access all candidates');
        $eagerLoad = ['user', 'job', 'interview_schedule'];

        if ($canAccessAll) {
            $eagerLoad[] = 'job.user';
            $eagerLoad[] = 'job.company';
        }

        $baseQuery = Candidate::whereNotIn('status', ['saved'])
            ->whereHas('job', function (Builder $q) use ($canAccessAll) {
                if ($canAccessAll) return;
                $q->where('user_id', auth()->user()->id);
            })
            ->when(!empty($jobID), function (Builder $q) use ($jobID) {
                $q->where('job_id', $jobID);
            })
            ->when(
                $search,
                function (Builder $query) use ($search) {
                    $query->where(function (Builder $query) use ($search) {
                        $query->whereHas('user', function (Builder $q) use ($search) {
                            $q->where(DB::raw('lower(name)'), 'like', '%' . strtolower($search) . '%')
                                ->orWhere(DB::raw('lower(email)'), 'like', '%' . strtolower($search) . '%');
                        });

                        $query->orWhereHas('job.company', function (Builder $q) use ($search) {
                            $q->where(DB::raw('lower(name)'), 'like', '%' . strtolower($search) . '%');
                        });

                        $query->orWhereHas('job.user', function (Builder $q) use ($search) {
                            $q->where(DB::raw('lower(email)'), 'like', '%' . strtolower($search) . '%');
                        });
                    });
                }
            )
            ->when(
                $company_name,
                function (Builder $query, $company_name) {
                    $query->whereHas('job.company', function (Builder $q) use ($company_name) {
                        $q->where(DB::raw('lower(name)'), 'like', '%' . strtolower($company_name) . '%');
                    });
                }
            )
            ->when(
                $jobseeker_name,
                function (Builder $query, $jobseeker_name) {
                    $query->whereHas('user', function (Builder $q) use ($jobseeker_name) {
                        $q->where(DB::raw('lower(name)'), 'like', '%' . strtolower($jobseeker_name) . '%');
                    });
                }
            )
            ->when(
                $status,
                function (Builder $query, $status) {
                    $query->where('status', $status);
                }
            )
            ->when(
                $created_at,
                function (Builder $query, $created_at) {
                    $query->whereDate('created_at', $created_at);
                }
            )
            ->with($eagerLoad)
            ->orderByRaw('created_at ' . $order);

        Helper::filterPreset($baseQuery, $filterPreset);

        $result = $baseQuery
            ->paginate($limit);

        if ($limit <= 0) {
            $result = $baseQuery->get();
        }

        return $result;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCandidateRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        $maxApplyJobFrequency = Setting::where('name', 'max-apply-job-frequency')->first()->value ?? 1;
        $userApplyCount = Candidate::where('user_id', $user->id)
            ->when($maxApplyJobFrequency == 1, function (Builder $query) {
                $query->where('created_at', '>=', now()->startOfMonth());
            }, function (Builder $query) {
                $query->where('created_at', '>=', now()->startOfYear());
            })
            ->count();

        $maxUserApply = Setting::where('name', 'max-apply-job')->first()->value ?? 5;

        if ($userApplyCount >= $maxUserApply && !$user->isPremium()) {
            return response([
                'success' => false,
                'error' => 'max_applied',
                'message' => 'Anda sudah melamar pekerjaan sebanyak 5 kali pada bulan ini!'
            ], 403);
        }
        $message = $validated['status'] == 'saved' ? 'Berhasil menandai pekerjaan!' : 'Berhasil melamar pekerjaan!';

        $job = Job::firstWhere('id', $validated['job_id']);
        $isUserAlreadyAppliedInThisCompany = !empty($job->company_id) ? Candidate::where('user_id', $user->id)
            ->whereHas('job', function (Builder $q) use ($job) {
                $q->where('company_id', $job->company_id);
            })
            ->whereMonth('created_at', now()->month)
            ->count() > 0 : false;

        if ($isUserAlreadyAppliedInThisCompany) {
            return response([
                'success' => false,
                'error' => 'already_applied_in_this_company',
                'message' => 'Anda sudah melamar di perusahaan ini!'
            ], 403);
        }

        $candidate = Candidate::updateOrCreate([
            'job_id' => $validated['job_id'],
            'user_id' => $user->id,
        ], [
            'status' => $validated['status'],
        ]);

        if ($validated['status'] != 'saved') {
            $job->user->notify(new UserApplied($job, $user, $candidate, $job->user->company));
            $user->notify(new PostApplyNotification($job));
        }

        return [
            'success' => true,
            'message' => $message,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidate $candidate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Candidate $candidate)
    {
        $employer = auth()->user();
        $candidate->load([
            'interview_schedule',
            'user.resume.educations',
            'user.resume.certifications',
            'user.resume.skills',
            'user.resume.user_detail',
            'user.resume.work_experiences',
            'document_request',
            'test_assesment',
        ]);

        if ($employer->level <= 2) {
            $candidate->user->makeHidden(['email']);
        } else if (in_array($employer->level, [4, 5])) {
            $candidate->user->makeVisible(['phone']);
        }
        // $candidate->user->resume->user_detail->makeVisible(['street_address']);

        return $candidate;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Candidate $candidate)
    {
        $candidate->update([
            'status' => $request['status']
        ]);

        $job = $candidate->job;
        $owner = $job->user;

        $candidate->user->notify(new CandidateStatusUpdated($candidate, $job, $owner, $owner->company));
        // DB::transaction(function () use ($candidate, $request) {


        // $user = $candidate->user;
        // $job = $candidate->job;

        // $notification = Notification::create([
        //     'category' => 'candidates',
        //     'description' => 'Lamaran anda di pekerjaan '. $job->name .' telah di' . $request['status'],
        //     'sub_description' => 'Candidates - Apply Job',
        //     'username' => $user->name,
        //     'created_by' => $user->id,
        //     // 'hex' => '',
        //     // 'content' => '',
        // ]);

        // $notification->receivers()->create([
        //     'received_by' => $job->user->id
        // ]);

        // $notification->save();
        // });


        return [
            'success' => true,
            'message' => 'Berhasil merubah detail pekerjaan!'
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidate $candidate)
    {
        if ($candidate->status === 'saved') $candidate->delete();
        return [
            'success' => true,
            'message' => 'Berhasil menghapus penanda pekerjaan!'
        ];
    }
}
