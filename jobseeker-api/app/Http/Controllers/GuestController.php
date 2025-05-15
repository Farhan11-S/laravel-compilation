<?php

namespace App\Http\Controllers;

use App\Constants\ChannelGroups;
use App\Enums\PremiumAdPlace;
use App\Models\Blog;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobChannelGroup;
use App\Models\Resume;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class GuestController extends Controller
{
    public function welcome(Request $request)
    {
        $jobs = collect();
        $query = $request->query();
        $what = $query['what'] ?? '';
        $where = $query['where'] ?? '';
        $limit = $query['limit'] ?? 10;
        $page = $query['page'] ?? 1;
        $sort = $query['sort'] ?? 'created_at,desc';
        $jobTypes = $query['job_type'] ?? [];
        $locations = $query['location'] ?? [];
        $country = $query['country'] ?? [];
        $showExpiredJobs = $query['show_expired_jobs'] ?? 0;

        $explodedSort = explode(',', $sort);
        $sort_by = $explodedSort[0] ?? 'id';
        $order = $explodedSort[1] ?? 'asc';

        // $premiumJobs = Job::where('job_status', '!=', 'closed')
        //     ->where(function (Builder $query) {
        //         $query->whereDate('application_deadline', '>', now())
        //             ->orWhereNull('application_deadline');
        //     })
        //     ->where(function (Builder $query) {
        //         $query->whereDate('published_at', '<=', now())
        //             ->orWhereNull('published_at');
        //     })
        //     ->whereHas('jobPremium')
        //     ->with('company', 'user', 'user.company', 'jobPremium')
        //     ->inRandomOrder()
        //     ->get();

        $eligibleEmployer = User::role('employer')
            ->whereHas('subscriptionTransactions', function (Builder $query) {
                $query->whereHas('subscriptionItem', function (Builder $query) {
                    $query->whereHas('subscriptionLevel', function (Builder $query) {
                        $query->where('premium_ads', 'like', '%' . PremiumAdPlace::FIRST_JOB_HOMEPAGE->value . '%');
                    });
                });
            })
            ->with([
                'jobs' => function ($query) {
                    $query->where('job_status', '!=', 'closed')
                        ->where(function (Builder $query) {
                            $query->whereDate('application_deadline', '>', now())
                                ->orWhereNull('application_deadline');
                        })
                        ->where(function (Builder $query) {
                            $query->whereDate('published_at', '<=', now())
                                ->orWhereNull('published_at');
                        })
                        ->with([
                            'company' => fn($q) => $q->select([
                                'id',
                                'name',
                                'logo',
                                'industry',
                                'sub_industry',
                                'office_address',
                                'description'
                            ])
                        ])
                        ->inRandomOrder()
                        ->limit(1);
                },
            ])
            ->inRandomOrder()
            ->first();

        $premiumJobs = $eligibleEmployer && !empty($eligibleEmployer)
            ? $eligibleEmployer->jobs->map(function ($job) {
                $job->job_premium = [
                    'tag_name' => 'Premium',
                    'color_hex' => '#fb923c',
                    'ended_at' => now(),
                ];
                return $job;
            }) : collect();

        $limitQuery = $limit - $premiumJobs->count();

        $baseQuery = Job::when(
            $what,
            function (Builder $query, string $what) {
                $query->where(DB::raw('lower(job_title)'), 'like', '%' . strtolower($what) . '%');
            }
        )
            ->when(
                $where,
                function (Builder $query, string $where) {
                    $query->where('location', 'like', '%' . strtolower($where) . '%');
                }
            )
            ->when(
                $jobTypes,
                function (Builder $query, $jobTypes) {
                    foreach ($jobTypes as $jobType) {
                        $query->where('job_type', 'LIKE', '%' . $jobType . '%');
                    }
                }
            )
            ->when(
                $locations,
                function (Builder $query, $locations) {
                    $query->where(function (Builder $query) use ($locations) {
                        foreach ($locations as $location) {
                            $query->orWhere('location', 'LIKE', '%' . $location . '%');
                        }
                    });
                }
            )
            ->when(
                $country,
                function (Builder $query, $country) {
                    $query->where(function (Builder $query) use ($country) {
                        foreach ($country as $countri) {
                            $query->orWhere('country', 'LIKE', '%' . $countri . '%');
                        }
                    });
                }
            )
            ->where('job_status', '!=', 'closed')
            ->when(
                !$showExpiredJobs,
                function (Builder $query) {
                    $query->where(function (Builder $query) {
                        $query->where(function (Builder $query) {
                            $query->whereDate('application_deadline', '>', now())
                                ->orWhereNull('application_deadline');
                        })
                            ->where(function (Builder $query) {
                                $query->whereDate('published_at', '<=', now())
                                    ->orWhereNull('published_at');
                            });
                    });
                }
            )
            ->with([
                'company' => fn($q) => $q->select([
                    'id',
                    'name',
                    'logo',
                    'industry',
                    'sub_industry',
                    'office_address',
                    'description'
                ]),
                'jobPremium' => fn($q) => $q->select([
                    'job_id',
                    'tag_name',
                    'color_hex',
                    'ended_at',
                ]),
            ])
            ->whereNotIn('id', $premiumJobs->pluck('id'))
            ->orderBy($sort_by, $order)
            ->latest();

        Model::withoutTimestamps(fn() => $baseQuery->clone()->increment('view'));
        $totalCount = $baseQuery->clone()->count();
        $jobs = $baseQuery
            ->skip(($page - 1) * ($limitQuery))
            ->limit($limitQuery)
            ->get();

        $mergedJobs = $premiumJobs->merge($jobs);

        $pagination = new LengthAwarePaginator($mergedJobs, $totalCount, $limit, $page);

        return $pagination;
    }

    public function viewjob(string $id, Request $request)
    {
        $source = $request->query('source');
        if ($source && ChannelGroups::isValidChannelGroup($source)) {
            $jobChannelGroup = JobChannelGroup::where('date', now()->toDateString())
                ->where('job_id', $id)
                ->first();
            if ($jobChannelGroup) {
                $jobChannelGroup->increment($source);
            } else {
                $jobChannelGroup = new JobChannelGroup();
                $jobChannelGroup->date = now()->toDateString();
                $jobChannelGroup->job_id = $id;
                $jobChannelGroup->$source = 1;
                $jobChannelGroup->save();
            }
        }
        $job = Job::with('company')->findOrFail($id);
        Model::withoutTimestamps(function () use ($job) {
            $job->view = $job->view + 1;
            $job->save();
        });

        $job->related_jobs = Job::where('id', '!=', $job->id)
            ->where('job_status', '!=', 'closed')
            ->where(function (Builder $query) use ($job) {
                $explodedTitle = explode(' ', $job->job_title);
                $query->where('location', $job->location);

                foreach ($explodedTitle as $title) {
                    $query->orWhere('job_title', 'like', '%' . $title . '%');
                }
            })
            ->select([
                'id',
                'job_title',
                'location',
                'job_type',
                'company_id',
                'created_at',
            ])
            ->with('company:id,name,logo')
            ->limit(3)
            ->inRandomOrder()
            ->get();

        return $job;
    }

    public function getAllLocationInJobs()
    {
        $locations = Job::select('location')
            ->groupBy('location')
            ->get();

        return [
            'data' => $locations->pluck('location'),
        ];
    }

    public function getWebsiteData()
    {
        return [
            'settings' => Setting::whereIn('name', [
                'website-name',
                'website-logo',
                'email-keluhan',
                'nomor-keluhan',
                'footer-title',
                'footer-subtitle',
                'env-google-analytics-measurement-id',
                'env-google-ads-client-id',
                'contact-us-title',
                'contact-us-description',
                'max-job-post-per-month',
                'footer-telegram',
                'footer-facebook',
                'footer-x',
                'footer-linkedin',
            ])->get(),
        ];
    }

    public function getFAQPricing()
    {
        function collectionSearch($settings, string $roleName)
        {
            return $settings->filter(function (Setting $setting) use ($roleName) {
                $explodedName = explode('-', $setting->name);

                return $explodedName[0] == $roleName;
            })->pluck('value', 'name');
        }

        $settings = Setting::where('name', 'like', '%-pricing-question-%')->orWhere('name', 'like', '%-pricing-answer-%')->get();

        $FAQs = [
            'employer' => [],
            'jobseeker' => [],
            'rawEmployer' => collectionSearch($settings, 'employer'),
            'rawJobseeker' => collectionSearch($settings, 'jobseeker'),
        ];

        foreach ($settings as $setting) {
            $explodedName = explode('-', $setting->name);

            $FAQs[$explodedName[0]][$explodedName[3]][$explodedName[2]] = $setting->value;
        }

        return [
            'faqs' => $FAQs,
        ];
    }

    public function viewCVPublic(string $id)
    {
        $avatarService = new AvatarService();

        $resume = Resume::with('certifications')
            ->with('educations')
            ->with('skills')
            ->with('language_skills')
            ->with('user_detail')
            ->with('work_experiences')
            ->with('user')
            ->find($id);

        if (empty($resume) || !$resume->is_shared || !$resume->user->isPremium()) {
            abort(404);
        }

        if (!empty($resume->user)) $resume->user = $avatarService->getUserWithAvatar($resume->user);

        $resume->user->makeVisible(['phone']);
        if (!empty($resume->user_detail) && $resume->user_detail != null) {
            $resume->user_detail->makeVisible(['street_address']);
        }

        return [
            'data' => $resume,
        ];
    }

    public function getBlogList(Request $request)
    {
        $avatarService = new AvatarService();

        $blogs = collect();
        $query = $request->query();
        $limit = $query['limit'] ?? 10;
        $search = $query['search'] ?? '';
        $categoryId = $query['category_id'] ?? '';
        $sort_by = $query['sort_by'] ?? 'created_at';
        $order = $query['order'] ?? 'desc';

        $baseQuery = Blog::when(
            $search,
            function (Builder $query, string $search) {
                $query->where(DB::raw('lower(title)'), 'like', '%' . strtolower($search) . '%');
            }
        )
            ->whereHas('categories', function (Builder $query) use ($categoryId) {
                $query->when($categoryId, function (Builder $query) use ($categoryId) {
                    $query->where('category_id', $categoryId);
                });
            })
            ->with('categories')
            // ->with('createdBy.company')
            ->orderByRaw($sort_by . ' ' . $order);

        // $increment = clone $baseQuery->limit($limit);
        $blogs = $baseQuery->paginate($limit);

        $blogs->getCollection()->transform(function ($blog) use ($avatarService) {
            $blog->load('createdBy.company');
            if (!empty($blog->createdBy)) $blog->createdBy = $avatarService->getUserWithAvatar($blog->createdBy);
            return $blog;
        });

        // $increment->increment('view');

        return $blogs;
    }

    public function viewBlog(string $id)
    {
        $avatarService = new AvatarService();

        $blog = Blog::with('createdBy', 'createdBy.company', 'categories')->findOrFail($id);
        if (!empty($blog->createdBy)) $blog->createdBy = $avatarService->getUserWithAvatar($blog->createdBy);
        // $job->view = $job->view + 1;
        // $job->save();
        return $blog;
    }

    public function getPopularTags()
    {
        $tags = Tag::withCount('blogs')
            ->orderBy('blogs_count', 'desc')
            ->limit(10)
            ->get();
        return $tags;
    }

    public function searchCompany(Request $request)
    {
        $companies = collect();
        $query = $request->query();
        $what = $query['what'] ?? '';
        $where = $query['where'] ?? '';
        $limit = $query['limit'] ?? 10;

        $baseQuery = Company::when(
            $what,
            function (Builder $query, string $what) {
                $query->where(DB::raw('lower(name)'), 'like', '%' . strtolower($what) . '%');
            }
        )
            ->when(
                $where,
                function (Builder $query, string $where) {
                    $query->where(DB::raw('lower(office_address)'), 'like', '%' . strtolower($where) . '%');
                }
            )
            ->with(['jobs'])
            ->latest();

        $companies = $baseQuery->paginate($limit);

        return [
            'data' => $companies,
            'total' => $companies->total(),
            'message' => 'Data fetched successfully',
        ];
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $filename = time() . $request['image']->getClientOriginalName();
            $request['image']->storeAs('public', $filename);
        }

        return [
            'data' => $filename,
            'message' => 'Image uploaded successfully',
        ];
    }

    public function deleteImage(Request $request)
    {
        $validated = $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|string',
        ]);

        $deletedList = [];
        foreach ($validated['images'] as $image) {
            $path = storage_path('app/public/' . $image);
            if (file_exists($path)) {
                unlink($path);
                $deletedList[] = $image;
            }
        }

        return [
            'message' => 'Image deleted successfully',
            'deleted' => $deletedList,
        ];
    }
}
