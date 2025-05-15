<?php

namespace Database\Seeders;

use App\Models\SubscriptionItem;
use Illuminate\Database\Seeder;

class SubscriptionItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $last = SubscriptionItem::all()->last();
        if ($last == null) {
            $item = SubscriptionItem::create([
                "type" => "DIGITAL_PRODUCT",
                "name" => "One Month Subscription",
                "net_unit_amount" => 14000,
                "category" => "Subscribtion",
                "subcategory" => "One Month",
                "schedule_id" => 1,
            ]);

            $item->save();
            $last = $item;
        }

        if ($last != null && $last->id == 1) {
            $item = SubscriptionItem::create([
                "type" => "DIGITAL_PRODUCT",
                "name" => "Six Month Subscription",
                "net_unit_amount" => 59000,
                "category" => "Subscribtion",
                "subcategory" => "Six Months",
                "schedule_id" => 2,
            ]);

            $item->save();
            $last = $item;
        }

        if ($last != null && $last->id == 2) {
            $item = SubscriptionItem::create([
                "type" => "DIGITAL_PRODUCT",
                "name" => "One Year Subscription",
                "net_unit_amount" => 99000,
                "category" => "Subscribtion",
                "subcategory" => "One Year",
                "schedule_id" => 3,
            ]);

            $item->save();
            $last = $item;
        }
        if ($last != null && $last->id == 3) {
            $item = SubscriptionItem::create([
                "type" => "DIGITAL_PRODUCT",
                "name" => "One Month Subscription Employer",
                "net_unit_amount" => 14000,
                "category" => "Subscribtion",
                "subcategory" => "One Month",
                "schedule_id" => 1,
                "role_id" => 2,
            ]);

            $item->save();
            $last = $item;
        }

        if ($last != null && $last->id == 4) {
            $item = SubscriptionItem::create([
                "type" => "DIGITAL_PRODUCT",
                "name" => "Six Month Subscription Employer",
                "net_unit_amount" => 59000,
                "category" => "Subscribtion",
                "subcategory" => "Six Months",
                "schedule_id" => 2,
                "role_id" => 2,
            ]);

            $item->save();
            $last = $item;
        }

        if ($last != null && $last->id == 5) {
            $item = SubscriptionItem::create([
                "type" => "DIGITAL_PRODUCT",
                "name" => "One Year Subscription Employer",
                "net_unit_amount" => 99000,
                "category" => "Subscribtion",
                "subcategory" => "One Year",
                "schedule_id" => 3,
                "role_id" => 2,
            ]);

            $item->save();
            $last = $item;
        }
    }
}
