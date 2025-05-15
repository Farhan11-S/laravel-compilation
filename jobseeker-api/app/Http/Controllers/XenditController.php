<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionItem;
use App\Models\User;
use App\Services\XenditService;
use Illuminate\Http\Request;

class XenditController extends Controller
{
    public function index(Request $request)
    {
        $service = new XenditService();
        $event = explode('.', $request->event);
        $data = $request->data;
        $user = User::where('customer_id', $data['customer_id'])->firstOrFail();

        switch ($event[1]) {
            case 'plan':
                $service->planCallbackHandler($request, $user);
                break;
        }

        return;
    }

    public function generateLinkingUI(Request $request)
    {
        $service = new XenditService();
        $actions = '';
        $redirect = '';
        $user = User::findOrFail(auth()->user()->id);

        if (empty($request->subscription_item_id)) abort(404, 'Subscription Item not found!');
        if ($user->package_type != 0) abort(403, 'User already subscribed to a plan!');

        $item = SubscriptionItem::with('subscriptionSchedule')->findOrFail($request->subscription_item_id);

        $response = $service->createPlanLinkingUI($user, $item);

        $data = $response->object();
        $actions = $data->actions;
        $redirect = $data->actions[0]->url;

        return [
            'data' => [
                'redirect' => $redirect,
                'actions' => $actions,
            ]
        ];
    }

    public function getSubscriptionItems(Request $request)
    {
        $query = $request->query();
        $role_id_param = $query['role_id'] ?? 3;
        $data = SubscriptionItem::with([
            'subscriptionSchedule',
            'coupon'
        ])->where([
            'category' => 'Subscribtion',
            'role_id' => $role_id_param,
        ])->get();

        return [
            'data' => $data,
            'role_id' => $role_id_param,
        ];
    }
}
