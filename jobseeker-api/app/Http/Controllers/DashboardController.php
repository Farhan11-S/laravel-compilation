<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function analyticDataCompany(Request $request)
    {
        $query = $request->query();
        $company_id = $query['company_id'] ?? '';
        $user_id = $query['user_id'] ?? '';
        $user = auth()->user();

        if ($user->hasRole('employer')) {
            $user_id = $user->id;
        }

        // Most Popular Jobs
        $views = Job::select('id', 'job_title', 'view', 'company_id', 'user_id')
            ->with([
                'company:id,name',
                'user:id,email'
            ])
            ->when($company_id, fn($query) => $query->where('company_id', $company_id))
            ->when($user_id, fn($query) => $query->where('user_id', $user_id))
            ->orderBy('view', 'DESC')
            ->limit(10)
            ->withCount([
                'candidates as total_candidates_count' => function (Builder $query) {
                    $query->whereNot('status', 'saved');
                },
            ])
            ->get();

        // Count Status Candidate
        $candidates = Job::with('candidates')
            ->when($company_id, fn($query) => $query->where('company_id', $company_id))
            ->when($user_id, fn($query) => $query->where('user_id', $user_id))
            ->get()
            ->pluck('candidates')
            ->flatten();

        $countedCandidatesData = $candidates
            ->countBy('status');
        $countedCandidates = collect([
            [
                'key' => 'reviewed',
                'name' => 'Reviewed',
                'value' => 0
            ],
            [
                'key' => 'waiting_review',
                'name' => 'Waiting Review',
                'value' => 0
            ],
            [
                'key' => 'accepted',
                'name' => 'Accepted',
                'value' => 0
            ],
            [
                'key' => 'rejected',
                'name' => 'Rejected',
                'value' => 0
            ],
        ]);
        $totalNonSavedCandidates = 0;
        $countedCandidates = $countedCandidates->map(function ($v, int $key) use ($countedCandidatesData, &$totalNonSavedCandidates) {
            $newVal = $countedCandidatesData[$v['key']] ?? 0;
            $totalNonSavedCandidates += $newVal;
            return [
                'name' => $v['name'],
                'key' => $v['key'],
                'value' => $newVal,
            ];
        });

        // Count Candidate Appliance in the Last 2 Weeks
        $filteredCandidates = $candidates
            ->sortBy('created_at')
            ->filter(
                function ($v) {
                    $firstDay = date("Y-m-d", strtotime('-2 weeks'));
                    $lastDay = date("Y-m-d", strtotime('today'));
                    $dday = date("Y-m-d", strtotime($v->created_at));
                    return  $dday >= $firstDay && $dday <= $lastDay;
                }
            );
        $applicationsOverviewData = $filteredCandidates
            ->sortBy('created_at')->countBy(function ($v) {
                return date("M d", strtotime($v->created_at));
            });

        $dateList = CarbonPeriod::between(date("Y-m-d", strtotime('-2 weeks')), date("Y-m-d", strtotime('today')))->toArray();

        $applicationsOverview = collect($dateList)->mapWithKeys(fn($v) => [date('M d', $v->timestamp) => 0])->merge($applicationsOverviewData);
        $applicationsOverviewMapped = array();
        foreach ($applicationsOverview as $k => $v) {
            array_push($applicationsOverviewMapped, [$k, $v]);
        }

        $applicationToday = Candidate::whereDate('created_at', Carbon::today())
            ->when($company_id, fn($query) => $query->whereHas('job', fn($query) => $query->where('company_id', $company_id)))
            ->when($user_id, fn($query) => $query->whereHas('job', fn($query) => $query->where('user_id', $user_id)))
            ->count();
        $jobPostedToday = Job::whereDate('created_at', Carbon::today())
            ->when($company_id, fn($query) => $query->where('company_id', $company_id))
            ->when($user_id, fn($query) => $query->where('user_id', $user_id))
            ->count();
        $jobPostedAllTime = Job::when($company_id, fn($query) => $query->where('company_id', $company_id))
            ->when($user_id, fn($query) => $query->where('user_id', $user_id))
            ->count();
        $tenLatestApplication = Candidate::with('job:id,job_title,company_id,user_id', 'user', 'job.user:id,name,email', 'job.company:id,name')
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->when($company_id, fn($query) => $query->whereHas('job', fn($query) => $query->where('company_id', $company_id)))
            ->when($user_id, fn($query) => $query->whereHas('job', fn($query) => $query->where('user_id', $user_id)))
            ->get(['id', 'job_id', 'user_id', 'status', 'created_at']);

        return [
            'data' => [
                'most_popular_pages' => $views,
                'count_candidates' => [
                    'data' =>  $countedCandidates,
                    'total' => $totalNonSavedCandidates
                ],
                'applications_overview' => $applicationsOverviewMapped,
                'application_today' => $applicationToday,
                'job_posted_today' => $jobPostedToday,
                'job_posted_all_time' => $jobPostedAllTime,
                'latest_application' => $tenLatestApplication
            ],
            'message' => 'success'
        ];
    }

    public function analyticDataAdmin(Request $request)
    {
        $registeredUsersToday = User::whereDate('created_at', Carbon::today())->count();
        $registeredCompaniesThisMonth = Company::whereMonth('created_at', Carbon::now()->month)->count();

        // $registeredUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)->count();
        $registeredJobSeekerToday = User::whereDate('created_at', Carbon::today())
            ->where(function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query
                        ->where('name', 'job seeker');
                })->orWhere('role_id', 3);
            })->count();
        $registeredEmployerToday = User::whereDate('created_at', Carbon::today())
            ->where(function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query->where('name', 'employer');
                })->orWhere('role_id', 2);
            })
            ->where(function ($query) {
                $query->whereHas('providers', function ($query) {
                    $query->whereNot('provider', 'admin');
                })
                    ->orDoesntHave('providers');
            })
            ->count();
        $registeredJobSeekerThisMonth = User::whereMonth('created_at', Carbon::now()->month)
            ->where(function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query
                        ->where('name', 'job seeker');
                })->orWhere('role_id', 3);
            })->count();
        $registeredEmployerThisMonth = User::whereMonth('created_at', Carbon::now()->month)
            ->where(function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query->where('name', 'employer');
                })->orWhere('role_id', 2);
            })->count();

        return [
            'data' => [
                'registered_users_today' => $registeredUsersToday,
                'registered_companies_this_month' => $registeredCompaniesThisMonth,
                'registered_job_seeker_this_month' => $registeredJobSeekerThisMonth,
                'registered_employer_this_month' => $registeredEmployerThisMonth,
                'registered_job_seeker_today' => $registeredJobSeekerToday,
                'registered_employer_today' => $registeredEmployerToday,
            ],
            'message' => 'success'
        ];
    }

    public function analyticDataEmployer()
    {
        $user = auth()->user();

        $totalViewsByThisUser = Job::where('user_id', $user->id)->sum('view');
        $getJobOrderedByViews = Job::where('user_id', $user->id)
            ->orderBy('view', 'DESC')
            ->limit(5)
            ->get(['id', 'job_title', 'view']);

        return [
            'data' => [
                'total_views' => $totalViewsByThisUser,
                'most_popular_jobs' => $getJobOrderedByViews
            ],
            'message' => 'success'
        ];
    }
}
