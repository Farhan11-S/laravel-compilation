<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $limit = $query['limit'] ?? 0;
        $search = $query['search'] ?? '';
        $type = $query['type'] ?? 'blog';
        $isPagination = $query['isPagination'] ?? false;
        $trashed = $query['trashed'] ?? false;

        $query = Category::when(
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
            )
            ->where('type', $type)
            ->when($type === 'blog', function (Builder $query) {
                $query->withCount('blogs');
                $query->orderBy('blogs_count', 'desc');
            });

        $result = $query
            ->when($limit > 0, fn(Builder $query) => $query->limit($limit))
            ->get();

        if ($isPagination) {
            $result = $query->with(['createdBy'])->paginate($limit);
        }

        return $result;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();

        $category = Category::create($validated);

        return [
            'data' => $category,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return [
            'data' => $category,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();

        $category->update($validated);

        return [
            'success' => true,
            'data' => $category,
            'message' => 'Berhasil merubah nama kategori'
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return [
            'data' => $category,
        ];
    }
}
