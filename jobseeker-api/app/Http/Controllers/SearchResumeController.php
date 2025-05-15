<?php

namespace App\Http\Controllers;

use App\Exports\SearchResumeExport;
use App\Models\Resume;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SearchResumeController extends Controller
{
    /**
     * Display a listing of the data.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $limit = $query['limit'] ?? 10;
        $what = $query['what'] ?? '';
        $where = $query['where'] ?? '';
        $job_title = $query['job_title'] ?? '';
        $job_title_array = explode(";", $job_title);
        $isExport = $query['export'] ?? 0;
        $user = auth()->user();

        $query = Resume::with('user_detail')
            ->whereHas('work_experiences')
            ->with([
                'work_experiences',
                'user',
            ])
            ->when(
                $what,
                function (Builder $query) use ($what) {
                    $query->whereHas('work_experiences', function (Builder $query) use ($what) {
                        $query->where(DB::raw('lower(job_title)'), 'like', '%' . strtolower($what) . '%');
                    });
                }
            )
            ->when(
                $where,
                function (Builder $query) use ($where) {
                    $query->whereHas('work_experiences', function (Builder $query) use ($where) {
                        $query->where(DB::raw('lower(city)'), 'like', '%' . strtolower($where) . '%');
                    });
                }
            )
            ->when(
                $job_title,
                function (Builder $query) use ($job_title_array) {
                    $query->whereHas('work_experiences', function (Builder $query) use ($job_title_array) {
                        $query->whereIn(DB::raw('lower(job_title)'), $job_title_array);
                    });
                }
            );

        $collectionQuery = $query->clone();
        $paginationData = tap($query->paginate($limit), function ($paginatedInstance) use ($user) {
            return $paginatedInstance->getCollection()->transform(function ($value) use ($user) {
                if ($user->level > 2) {
                    $value->user?->makeVisible(['phone']);
                }
                return $value;
            });
        });
        $collectionData = $collectionQuery->get();
        $job_title_list = $collectionData->pluck('work_experiences.*.job_title')->flatten()->countBy(function (string $value) {
            return ucfirst($value);
        });

        if ($isExport) {
            return Excel::download(new SearchResumeExport($query->clone()), 'search-resume-export.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return [
            'data' => $paginationData,
            'job_titles' => $job_title_list
        ];
    }
}
