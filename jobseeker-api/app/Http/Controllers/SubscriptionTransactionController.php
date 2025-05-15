<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionTransactionStatus;
use App\Http\Requests\Admin\SubscriptionTransaction\StoreTransactionRequest;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\SubscriptionBank;
use App\Models\SubscriptionItem;
use App\Models\SubscriptionTransaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class SubscriptionTransactionController extends Controller
{
    public function getUserUnresolvedTransaction()
    {
        $user = auth()->user();
        $user->load([
            'unresolvedSubscriptionTransactions',
            'unresolvedSubscriptionTransactions.subscriptionItem',
            'unresolvedSubscriptionTransactions.createdBy',
            'unresolvedSubscriptionTransactions.subscriptionBank',
        ]);
        return [
            'data' => $user->unresolvedSubscriptionTransactions
        ];
    }

    public function getListTransaction(Request $request)
    {
        $query = $request->query();
        $limit = $query['limit'] ?? 0;

        $data = SubscriptionTransaction::with([
            'subscriptionItem',
            'createdBy'
        ])->latest()->paginate($limit);

        return [
            'data' => $data
        ];
    }

    public function createTransaction(StoreTransactionRequest $request)
    {
        $validated = $request->validated();

        if (auth()->user()->subscriptionTransactions()->where('status', '!=', SubscriptionTransactionStatus::SELESAI)->exists()) {
            abort(403);
        }

        $subscriptionItem = SubscriptionItem::findOrFail($validated['subscription_item_id']);
        $subscriptionBank = SubscriptionBank::findOrFail($validated['subscription_bank_id']);
        $subscriptionSchedule = $subscriptionItem->subscriptionSchedule;
        $amount = $subscriptionItem->net_unit_amount;
        $validated['total_due'] = $amount;

        $coupon = Coupon::firstWhere('id', $subscriptionItem->coupon_id);
        if ($coupon) {
            $validated['coupon_id'] = $subscriptionItem->coupon_id;
            $validated['total_due'] = $amount - round($amount * ($coupon->value / 100));
        }

        $validated['started_at'] = date('Y-m-d H:i:s', strtotime('now'));
        $validated['expired_at'] = date('Y-m-d H:i:s', strtotime('+1 day'));
        $validated['ended_at'] = date('Y-m-d H:i:s', strtotime('+' . $subscriptionSchedule->interval_count . $subscriptionSchedule->interval));
        $validated['account_number'] = $subscriptionBank->account_number;
        $validated['account_name'] = $subscriptionBank->account_name;
        $validated['created_by'] = auth()->user()->id;
        $trx = SubscriptionTransaction::create($validated);

        return [
            'data' => $trx,
        ];
    }

    public function uploadProofOfPayment(string $id, Request $request)
    {
        if (auth()->user()->subscriptionTransactions()->where('status', SubscriptionTransactionStatus::MENUNGGU_KONFIRMASI)->exists()) {
            abort(403);
        }

        $request->validate([
            'img' => ['required'],
        ]);

        if ($request->hasFile('img')) {
            $filename = time() . $request['img']->getClientOriginalName();
            $request['img']->storeAs('public', $filename);

            $trx = SubscriptionTransaction::findOrFail($id);
            $trx->proof_of_payment_img = $filename;
            $trx->status = SubscriptionTransactionStatus::MENUNGGU_KONFIRMASI;
            $trx->save();

            return [
                'data' => $trx,
            ];
        }

        return [
            'status' => 'failed',
            'data' => [],
        ];
    }

    public function getTransactionStatusByID(string $id)
    {
        $trx = SubscriptionTransaction::with([
            'subscriptionItem',
            'subscriptionItem.subscriptionSchedule',
            'subscriptionBank',
            'subscriptionBank.bank',
            'createdBy',
            'coupon',
        ])->findOrFail($id);

        return [
            'data' => $trx,
        ];
    }

    public function updateTransactionStatus(string $id, Request $request)
    {
        $request->validate([
            'status' => ['required', 'string', new Enum(SubscriptionTransactionStatus::class)],
        ]);

        $trx = SubscriptionTransaction::findOrFail($id);
        $trx->status = $request->status;
        $trx->save();

        if ($trx->status == SubscriptionTransactionStatus::SELESAI->value) {
            $coupon = Coupon::firstWhere('id', $trx->coupon_id);
            if ($coupon) {
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'referral_id' => null,
                    'type' => 'discount',
                    'user_id' => $trx->created_by,
                    'ended_at' => $trx->ended_at,
                ]);
            }
            $trx->createdBy->package_type = $trx->subscription_item_id;
            $trx->createdBy->save();
        }

        return [
            'data' => $trx,
        ];
    }
}
