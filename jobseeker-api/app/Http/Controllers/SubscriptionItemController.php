<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionItem\UpdateSubscriptionItem;
use App\Models\SubscriptionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $limit = $query['limit'] ?? 0;

        $data = SubscriptionItem::with([
            'subscriptionSchedule',
            'coupon',
        ])->paginate($limit);

        return [
            'data' => $data
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionItem $request)
    {
        // $validated = $request->validated();

        // $subscriptionItem = SubscriptionItem::create($validated);

        // return $subscriptionItem;
    }

    /**
     * Display the specified resource.
     */
    public function show(SubscriptionItem $subscriptionItem)
    {
        return [
            'data' => $subscriptionItem->load([
                'subscriptionSchedule',
                'coupon',
            ])
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubscriptionItem $subscriptionItem)
    {
        return [
            'data' => $subscriptionItem->load([
                'subscriptionSchedule',
                'coupon',
            ])
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionItem $request, SubscriptionItem $subscriptionItem)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($subscriptionItem, $validated) {
            $subscriptionItem->currencyAmounts()->delete();
            $subscriptionItem->currencyAmounts()->createMany($validated['currency_amounts']);
            unset($validated['currency_amounts']);

            $subscriptionItem->update($validated);
        });

        return $subscriptionItem;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscriptionItem $subscriptionItem)
    {
        //
    }
}
