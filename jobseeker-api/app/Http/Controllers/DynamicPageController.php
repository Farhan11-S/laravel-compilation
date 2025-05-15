<?php

namespace App\Http\Controllers;

use App\Http\Requests\DynamicPage\StoreDynamicPageRequest;
use App\Http\Requests\DynamicPage\UpdateDynamicPageRequest;
use App\Models\DynamicPage;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DynamicPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $search = $query['search'] ?? '';
        $limit = $query['limit'] ?? 0;

        $query = DynamicPage::when(
            $search,
            function (Builder $query) use ($search) {
                $query->where(DB::raw('lower(title)'), 'like', '%' . strtolower($search) . '%');
            }
        );

        $data = ($limit > 0) ?
            $query->paginate($limit) :
            $query->get();
        return [
            'data' => $data,
        ];
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
    public function store(StoreDynamicPageRequest $request)
    {
        $dynamicPage = new DynamicPage();

        $validated = $request->validated();

        $dynamicPage->fill($validated);

        $dynamicPage->save();

        return $dynamicPage;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $link)
    {
        $dynamicPage = DynamicPage::where('link', $link)->firstOrFail();
        return [
            'data' => $dynamicPage,
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DynamicPage $dynamicPage)
    {
        return [
            'data' => $dynamicPage,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDynamicPageRequest $request, DynamicPage $dynamicPage)
    {
        $validated = $request->validated();

        $dynamicPage->update($validated);

        return $dynamicPage;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DynamicPage $dynamicPage)
    {
        $dynamicPage->delete();
    }
}
