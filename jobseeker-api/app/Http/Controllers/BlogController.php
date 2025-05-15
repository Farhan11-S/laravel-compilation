<?php

namespace App\Http\Controllers;

use App\Http\Requests\Blog\StoreBlogRequest;
use App\Http\Requests\Blog\UpdateBlogRequest;
use App\Models\Blog;
use App\Services\AvatarService;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $avatarService = new AvatarService();

        $blogs = collect();
        $query = $request->query();
        $limit = $query['limit'] ?? 10;
        $search = $query['search'] ?? '';
        $sort_by = $query['sort_by'] ?? 'created_at';
        $order = $query['order'] ?? 'asc';
        $trashed = $query['trashed'] ?? false;

        $blogs = Blog::when(
            $search,
            function (Builder $query, string $search) {
                $query->where(DB::raw('lower(title)'), 'like', '%' . strtolower($search) . '%');
            }
        )
            ->when(
                !auth()->user()->isSuperadmin(),
                function (Builder $query) {
                    $query->where('created_by', auth()->user()->id);
                }
            )
            ->when(
                $trashed,
                function (Builder $query) {
                    $query->onlyTrashed();
                }
            )
            ->with('createdBy', 'deletedBy', 'categories', 'tags')
            ->orderByRaw($sort_by . ' ' . $order)
            ->paginate($limit);

        $blogs->getCollection()->transform(function ($blog) use ($avatarService) {
            $blog->load('createdBy.company');
            if (!empty($blog->createdBy)) $blog->createdBy = $avatarService->getUserWithAvatar($blog->createdBy);
            return $blog;
        });

        return $blogs;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $filename = time() . $validated['thumbnail']->getClientOriginalName();
            $validated['thumbnail']->storeAs('public', $filename);
            $validated['thumbnail'] = $filename;
        }

        $blog = Blog::create($validated);

        foreach ($validated['tag_ids'] as $tagId) {
            $blog->tags()->attach($tagId);
        }

        foreach ($validated['category_ids'] as $categoryId) {
            $blog->categories()->attach($categoryId);
        }

        return [
            'data' => $blog,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        $blog->load(['createdBy', 'deletedBy', 'categories', 'tags']);
        return [
            'data' => $blog,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $filename = time() . $validated['thumbnail']->getClientOriginalName();
            $validated['thumbnail']->storeAs('public', $filename);
            $validated['thumbnail'] = $filename;
        }

        $tagIDsDB = $blog->tags()->get()->pluck('id');
        $tagIDsReq = collect($validated['tag_ids'] ?? []);

        foreach ($tagIDsDB->diff($tagIDsReq) as $tagId) {
            $blog->tags()->detach($tagId);
        }
        foreach ($tagIDsReq->diff($tagIDsDB) as $tagId) {
            $blog->tags()->attach($tagId);
        }

        $categoryIDsDB = $blog->categories()->get()->pluck('id');
        $categoryIDsReq = collect($validated['category_ids'] ?? []);

        foreach ($categoryIDsDB->diff($categoryIDsReq) as $categoryId) {
            $blog->categories()->detach($categoryId);
        }
        foreach ($categoryIDsReq->diff($categoryIDsDB) as $categoryId) {
            $blog->categories()->attach($categoryId);
        }

        if (!empty($validated['tag_ids'])) unset($validated['tag_ids']);
        if (!empty($validated['category_ids'])) unset($validated['category_ids']);
        $blog->update($validated);

        return [
            'data' => $blog,
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();
        return [
            'data' => $blog,
        ];
    }
}
