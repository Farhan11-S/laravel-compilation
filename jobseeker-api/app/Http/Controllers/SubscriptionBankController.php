<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\SubscriptionBank\StoreBankRequest;
use App\Http\Requests\Admin\SubscriptionBank\UpdateBankRequest;
use App\Models\SubscriptionBank;
use Illuminate\Http\Request;

class SubscriptionBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $limit = $query['limit'] ?? 0;

        $data = SubscriptionBank::with('bank')->latest()->paginate($limit);

        return [
            'data' => $data
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBankRequest $request)
    {
        $validated = $request->validated();

        $subscriptionBank = SubscriptionBank::create($validated);

        return [
            'data' => $subscriptionBank,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(SubscriptionBank $subscriptionBank)
    {
        return [
            'data' => $subscriptionBank,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBankRequest $request, SubscriptionBank $subscriptionBank)
    {
        $validated = $request->validated();

        $subscriptionBank->update($validated);

        return [
            'data' => $subscriptionBank,
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscriptionBank $subscriptionBank)
    {
        $subscriptionBank->delete();

        return [
            'data' => $subscriptionBank,
        ];
    }
}
