<?php

namespace App\Services;

use App\Models\SubscriptionItem;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class XenditService
{
    public function planCallbackHandler(Request $request, User $user)
    {
        $event = explode('.', $request->event);
        $data = $request->data;

        $item = SubscriptionItem::where('net_unit_amount', $data['amount'])->first();
        if ($item == null) $item = SubscriptionItem::first();

        self::saveSubscriptionPlan($data['id'], $data['amount'], $data['status'], $user->id, $item->id);

        switch ($event[2]) {
            case 'activated':
                $user->package_type = $item->id;
                break;
            case 'inactivated':
                $user->package_type = 0;
                break;
        }

        $user->save();
    }

    public function saveSubscriptionPlan($id, $amount, $status, $userID, $packageType)
    {
        return SubscriptionPlan::updateOrCreate(
            ['plan_id' => $id],
            [
                'amount' => $amount,
                'status' => $status,
                'user_id' => $userID,
                'package_type' => $packageType,
            ]
        );
    }

    public function planBodyBuilder(User $user, SubscriptionItem $item)
    {
        $t = time();
        $customer_id = self::getCustomerIDByUser($user);
        $schedule = $item->subscriptionSchedule;

        $frontendURL = config('app.url', 'http://localhost');
        if (str_starts_with($frontendURL, 'http')) $frontendURL = 'https://www.xendit.co/';

        return  [
            "reference_id" => "ref-$t",
            "customer_id" => "$customer_id",
            "recurring_action" => config('xendit.recurring_action'),
            "currency" => config('xendit.currency'),
            "amount" => $item->net_unit_amount,
            "schedule" => [
                "reference_id" => "test-$t",
                "interval" => $schedule->interval,
                "interval_count" => $schedule->interval_count,
                "total_recurrence" => $schedule->total_recurrence,
                "retry_interval" => $schedule->retry_interval,
                "retry_interval_count" => $schedule->retry_interval_count,
                "total_retry" => $schedule->total_retry,
                "failed_attempt_notifications" => $schedule->failed_attempt_notifications
            ],
            "notification_config" => [
                "locale" => "en",
                "recurring_created" => [
                    "WHATSAPP",
                    "EMAIL"
                ],
                "recurring_succeeded" => [
                    "WHATSAPP",
                    "EMAIL"
                ],
                "recurring_failed" => [
                    "WHATSAPP",
                    "EMAIL"
                ]
            ],
            "failed_cycle_action" => config('xendit.failed_cycle_action'),
            "immediate_action_type" => config('xendit.immediate_action_type'),
            "metadata" => null,
            "description" => config('xendit.description'),
            "success_return_url" => $frontendURL,
            "failure_return_url" => $frontendURL,
            "items" => [
                [
                    "type" => $item->type,
                    "name" => $item->name,
                    "net_unit_amount" => $item->net_unit_amount,
                    "quantity" => 1,
                    "url" => $frontendURL,
                    "category" =>  $item->category,
                    "subcategory" =>  $item->subcategory
                ]
            ]
        ];
    }

    public function createPlanLinkingUI(User $user, SubscriptionItem $item)
    {
        try {
            $data = self::planBodyBuilder($user, $item);
            $response = Http::withBasicAuth(config('xendit.api_key', ''), '')->post(config('xendit.url') . 'recurring/plans', $data);

            //Check for any error 400 or 500 level status code
            if ($response->failed()) {
                abort(500, $response->body());
            }

            $objectRes = $response->object();
            self::saveSubscriptionPlan($objectRes->id, $objectRes->amount, $objectRes->status, $user->id, $item->id);

            return $response;
        } catch (\Exception $e) {
            //$e->getMessage() - will output "cURL error 6: Could not resolve host" in case of invalid domain
            abort(500, $e->getMessage());
        }
    }

    public function getCustomerIDByUser(User $user)
    {
        if ($user->customer_id == null) {
            $randomUUID = uniqid();
            $phone = $user->phone ?? '+6200000000';
            $response = self::createCustomer([
                "reference_id" => "$randomUUID",
                "mobile_number" => "$phone",
                "email" => "$user->email",
                "type" => "INDIVIDUAL",
                "individual_detail" => [
                    "given_names" => "$user->name"
                ]
            ]);

            $user->customer_id = $response->object()->id;
            $user->save();
        }
        return $user->customer_id;
    }

    public function createCustomer($data)
    {
        try {
            $response = Http::withBasicAuth(config('xendit.api_key', ''), '')->post(config('xendit.url') . 'customers', $data);

            //Check for any error 400 or 500 level status code
            if ($response->failed()) {
                abort(500, $response->body());
            }

            return $response;
        } catch (\Exception $e) {
            //$e->getMessage() - will output "cURL error 6: Could not resolve host" in case of invalid domain
            abort(500, $e->getMessage());
        }
    }
}
