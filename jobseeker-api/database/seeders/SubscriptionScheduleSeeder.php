<?php

namespace Database\Seeders;

use App\Models\SubscriptionSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $last = SubscriptionSchedule::all()->last();
        if ($last == null) {
            $schedule = SubscriptionSchedule::create([
                "interval" => "MONTH",
                "interval_count" => 1,
                "retry_interval" => "DAY",
                "retry_interval_count" => 1,
                "total_retry" => 3,
                "failed_attempt_notifications" => [
                    1,
                    3
                ]
            ]);

            $schedule->save();
            $last = $schedule;
        }

        if ($last != null && $last->id == 1) {
            $schedule = SubscriptionSchedule::create([
                "interval" => "MONTH",
                "interval_count" => 6,
                "retry_interval" => "DAY",
                "retry_interval_count" => 7,
                "total_retry" => 3,
                "failed_attempt_notifications" => [
                    1,
                    3
                ]
            ]);

            $schedule->save();
            $last = $schedule;
        }

        if ($last != null && $last->id == 2) {
            $schedule = SubscriptionSchedule::create([
                "interval" => "MONTH",
                "interval_count" => 12,
                "retry_interval" => "DAY",
                "retry_interval_count" => 7,
                "total_retry" => 6,
                "failed_attempt_notifications" => [
                    1,
                    3,
                    4,
                    6
                ]
            ]);

            $schedule->save();
        }
    }
}
