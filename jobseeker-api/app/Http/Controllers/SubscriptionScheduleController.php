<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSubscriptionSchedule;
use App\Models\SubscriptionSchedule;
use Illuminate\Http\Request;

class SubscriptionScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $limit = $query['limit'] ?? 0;

        $data = SubscriptionSchedule::paginate($limit);

        return [
            'data' => $data
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SubscriptionSchedule $subscriptionSchedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubscriptionSchedule $subscriptionSchedule)
    {
        return [
            'data' => $subscriptionSchedule
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionSchedule $request, SubscriptionSchedule $subscriptionSchedule)
    {
        $validated = $request->validated();

        $subscriptionSchedule->update($validated);

        return $subscriptionSchedule;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscriptionSchedule $subscriptionSchedule)
    {
        //
    }
}
