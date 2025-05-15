<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionLevel\UpdateSubscriptionLevel;
use App\Models\SubscriptionLevel;
use Illuminate\Http\Request;

class SubscriptionLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $limit = $query['limit'] ?? 0;

        $data = SubscriptionLevel::paginate($limit);

        return [
            'data' => $data,
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SubscriptionLevel $subscriptionLevel)
    {
        return [
            'data' => $subscriptionLevel,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionLevel $request, SubscriptionLevel $subscriptionLevel)
    {
        $validated = $request->validated();

        $subscriptionLevel->update($validated);

        return $subscriptionLevel;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscriptionLevel $subscriptionLevel)
    {
        //
    }
}
