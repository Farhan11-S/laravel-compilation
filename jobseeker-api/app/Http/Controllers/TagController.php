<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $limit = $query['limit'] ?? 10;
        $isPagination = $query['isPagination'] ?? false;
        $search = $query['search'] ?? '';
        $trashed = $query['trashed'] ?? false;

        $query = Tag::when(
            $search,
            function (Builder $query, string $search) {
                $query->where(DB::raw('lower(name)'), 'like', '%' . strtolower($search) . '%');
            }
        )
            ->when(
                $trashed,
                function (Builder $query) {
                    $query->onlyTrashed();
                }
            );

        $result = $query->limit($limit)->get();

        if ($isPagination) {
            $result = $query->with(['createdBy'])->paginate($limit);
        }

        return $result;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request)
    {
        $validated = $request->validated();

        $tag = Tag::create($validated);

        return [
            'data' => $tag,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $validated = $request->validated();

        $tag->update($validated);

        return [
            'success' => true,
            'data' => $tag,
            'message' => 'Berhasil merubah nama tag'
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return [
            'data' => $tag,
        ];
    }
}
