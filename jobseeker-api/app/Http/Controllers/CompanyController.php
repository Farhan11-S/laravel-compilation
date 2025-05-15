<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Helper\Helper;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Database\Query\Builder;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $limit = $query['limit'] ?? 0;
        $search = $query['search'] ?? '';
        $sort_by = $query['sort_by'] ?? 'updated_at';
        $order = $query['order'] ?? 'desc';
        $trashed = $query['trashed'] ?? false;
        $filterPreset = $query['filter_preset'] ?? '';

        $query = Company::when(
            $search,
            function (Builder $query, string $search) {
                $query->where('name', 'like', '%' . strtolower($search) . '%');
            }
        )
            ->when(
                $trashed,
                function (Builder $query) {
                    $query->onlyTrashed();
                }
            )
            ->with('deletedBy')
            ->withCount([
                'jobs',
                'users as employers_count' => function (Builder $query) {
                    $query->role('employer');
                },
            ])
            ->orderByRaw($sort_by . ' ' . $order);

        Helper::filterPreset($query, $filterPreset);

        $result = $query->get(['id as value', 'name as label']);
        if ($limit > 0) {
            $result = $query->paginate($limit);
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
    public function store(StoreCompanyRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $filename = time() . $validated['logo']->getClientOriginalName();
            $validated['logo']->storeAs('public', $filename);
            $validated['logo'] = $filename;
        }

        if ($request->hasFile('banner')) {
            $filename = time() . $validated['banner']->getClientOriginalName();
            $validated['banner']->storeAs('public', $filename);
            $validated['banner'] = $filename;
        }

        DB::transaction(function () use ($validated) {
            $company = Company::firstOrCreate([
                'name' => $validated['name'],
            ], $validated);

            $company->save();

            $user = auth()->user();

            if ($user->role_id == Roles::SUPERADMIN) return;

            $user->role_id = Roles::EMPLOYER;
            $user->company_id = $company->id;
            $user->save();
        });

        return;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Company::with('jobs')->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Company::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, string $id)
    {
        $company = Company::findOrFail($id);

        $validated = $request->validated();
        if ($request->hasFile('logo')) {
            $filename = time() . $validated['logo']->getClientOriginalName();
            $validated['logo']->storeAs('public', $filename);
            $validated['logo'] = $filename;
        }

        if ($request->hasFile('banner')) {
            $filename = time() . $validated['banner']->getClientOriginalName();
            $validated['banner']->storeAs('public', $filename);
            $validated['banner'] = $filename;
        }

        $company->update($validated);

        return $company;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
