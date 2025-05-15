<?php

namespace App\Http\Controllers;

use App\Http\Requests\Menu\StoreMenuRequest;
use App\Http\Requests\Menu\UpdateMenuRequest;
use App\Models\Menu;
use App\Models\SubscriptionLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $trashed = $query['trashed'] ?? false;
        $limit = $query['limit'] ?? 0;
        $place = $query['place'] ?? '';

        $query = Menu::latest()
            ->when(
                $trashed,
                fn(Builder $query) => $query->onlyTrashed()
            )
            ->when(
                $place,
                function (Builder $query, $place) {
                    $query->where('place', $place);
                }
            );

        $result = $query->get();
        if ($limit > 0) {
            $result = $query->paginate($limit);
        }

        return [
            'data' => $result
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMenuRequest $request)
    {
        $validated = $request->validated();
        $menu = Menu::create($validated);

        return [
            'data' => $menu,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        return [
            'data' => $menu,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $validated = $request->validated();
        $menu->update($validated);

        return [
            'data' => $menu,
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();
        return [
            'data' => $menu,
        ];
    }

    public function getMenus()
    {
        $menus = Menu::where('parent', 0)->with(['children'])->get()->groupBy('place');
        return [
            'data' => $menus,
        ];
    }

    public function getMenusByPlace(string $place)
    {
        $user = auth()->user();
        $query = Menu::where('place', $place)
            ->where('parent', 0)
            ->orderBy('position', 'asc')
            ->with(['children']);

        if ($place == 'dashboard' && !$user->hasRole('admin')) {
            if ($user->isPremium()) {
                $sl = SubscriptionLevel::find($user->level);
                if ($sl === null || !$sl->show_resume_search_menu) {
                    $query->where(
                        'slug',
                        '!=',
                        'search-resume'
                    );
                }
            }
        }

        $menus = $query->get();
        return [
            'data' => $menus,
        ];
    }

    public function getAllMenusSlug()
    {
        $menus = Menu::get()->pluck('slug');
        return [
            'data' => $menus,
        ];
    }
}
