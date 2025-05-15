<?php

namespace App\Http\Controllers;

use App\Http\Requests\Coupon\StoreCouponRequest;
use App\Http\Requests\Coupon\UpdateCouponRequest;
use App\Models\Coupon;
use DateTime;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $trashed = $query['trashed'] ?? false;
        $expired = $query['expired'] ?? false;
        $limit = $query['limit'] ?? 0;
        $today = (new DateTime(now()))->format('Y-m-d');

        $sql = Coupon::latest()
            ->when(
                $trashed,
                fn(Builder $query) => $query->onlyTrashed()
            )
            ->when(
                $expired,
                fn(Builder $query) => $query->whereDate('expired_at', '<', $today),
                fn(Builder $query) => $query->whereDate('expired_at', '>=', $today),
            );

        $result = $sql->get();
        if ($limit > 0) {
            $result = $sql->paginate($limit);
        }
        return [
            'data' => $result
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCouponRequest $request)
    {
        $validated = $request->validated();
        $coupon = Coupon::create($validated);

        return [
            'data' => $coupon,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        return [
            'data' => $coupon,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $validated = $request->validated();
        $coupon->update($validated);

        return [
            'data' => $coupon,
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return [
            'data' => $coupon,
        ];
    }
}
